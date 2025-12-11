<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\WorkflowLog;
use App\Models\Task;
use App\Models\Notification;
use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class WorkflowService
{
    /**
     * Execute workflows for a specific trigger
     */
    public function executeTrigger(string $triggerType, Sponsorship $sponsorship, array $context = []): void
    {
        $workflows = Workflow::where('trigger_type', $triggerType)
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            $this->executeWorkflow($workflow, $sponsorship, $context);
        }
    }

    /**
     * Execute a single workflow
     */
    public function executeWorkflow(Workflow $workflow, Sponsorship $sponsorship, array $context = []): void
    {
        // Check trigger conditions
        if (!$this->checkTriggerConditions($workflow, $sponsorship, $context)) {
            WorkflowLog::create([
                'workflow_id' => $workflow->id,
                'sponsorship_id' => $sponsorship->id,
                'status' => WorkflowLog::STATUS_SKIPPED,
                'message' => 'Trigger conditions not met',
                'details' => $context,
            ]);
            return;
        }

        try {
            // Execute each action
            foreach ($workflow->actions as $action) {
                $this->executeAction($action, $workflow, $sponsorship, $context);
            }

            // Log success
            WorkflowLog::create([
                'workflow_id' => $workflow->id,
                'sponsorship_id' => $sponsorship->id,
                'status' => WorkflowLog::STATUS_SUCCESS,
                'message' => 'Workflow executed successfully',
                'details' => $context,
            ]);

            $workflow->incrementExecutionCount();

        } catch (\Exception $e) {
            WorkflowLog::create([
                'workflow_id' => $workflow->id,
                'sponsorship_id' => $sponsorship->id,
                'status' => WorkflowLog::STATUS_FAILED,
                'message' => $e->getMessage(),
                'details' => $context,
            ]);
        }
    }

    /**
     * Check if trigger conditions are met
     */
    protected function checkTriggerConditions(Workflow $workflow, Sponsorship $sponsorship, array $context): bool
    {
        $config = $workflow->trigger_config ?? [];

        switch ($workflow->trigger_type) {
            case Workflow::TRIGGER_STAGE_CHANGE:
                // Check if the new stage matches the configured stage
                if (isset($config['stage']) && isset($context['new_stage'])) {
                    return $context['new_stage'] === $config['stage'];
                }
                return true;

            case Workflow::TRIGGER_DEAL_STAGNANT:
                return $sponsorship->isStagnant();

            case Workflow::TRIGGER_DEAL_WON:
                return $sponsorship->stage === 'Closed Won';

            case Workflow::TRIGGER_DEAL_LOST:
                return $sponsorship->stage === 'Closed Lost';

            default:
                return true;
        }
    }

    /**
     * Execute a single action
     */
    protected function executeAction(array $action, Workflow $workflow, Sponsorship $sponsorship, array $context): void
    {
        $actionType = $action['type'] ?? '';

        switch ($actionType) {
            case Workflow::ACTION_CREATE_TASK:
                $this->createTask($action, $workflow, $sponsorship);
                break;

            case Workflow::ACTION_NOTIFY_USER:
                $this->notifyUser($action, $sponsorship);
                break;

            case Workflow::ACTION_NOTIFY_TEAM:
                $this->notifyTeam($action, $sponsorship);
                break;

            case Workflow::ACTION_UPDATE_PRIORITY:
                $this->updatePriority($action, $sponsorship);
                break;

            case Workflow::ACTION_SEND_EMAIL:
                $this->sendEmail($action, $sponsorship);
                break;
        }
    }

    /**
     * Create a follow-up task
     */
    protected function createTask(array $action, Workflow $workflow, Sponsorship $sponsorship): void
    {
        $daysUntilDue = $action['days_until_due'] ?? 3;
        $assignTo = $action['assign_to'] ?? null;

        Task::create([
            'user_id' => $workflow->user_id,
            'assigned_to' => $assignTo,
            'sponsorship_id' => $sponsorship->id,
            'workflow_id' => $workflow->id,
            'title' => $action['title'] ?? "Follow up with {$sponsorship->company_name}",
            'description' => $action['description'] ?? "Auto-generated task from workflow: {$workflow->name}",
            'due_date' => now()->addDays($daysUntilDue),
            'priority' => $action['priority'] ?? Task::PRIORITY_MEDIUM,
            'status' => Task::STATUS_PENDING,
        ]);
    }

    /**
     * Send notification to a specific user
     */
    protected function notifyUser(array $action, Sponsorship $sponsorship): void
    {
        $userId = $action['user_id'] ?? $sponsorship->user_id ?? auth()->id();
        
        Notification::send(
            $userId,
            $action['title'] ?? "Deal Update: {$sponsorship->company_name}",
            $action['message'] ?? "The deal with {$sponsorship->company_name} requires your attention.",
            $action['notification_type'] ?? Notification::TYPE_INFO,
            "/sponsorships"
        );
    }

    /**
     * Notify all team members
     */
    protected function notifyTeam(array $action, Sponsorship $sponsorship): void
    {
        $users = User::whereIn('role', ['admin', 'consultant'])->get();

        foreach ($users as $user) {
            Notification::send(
                $user->id,
                $action['title'] ?? "Team Alert: {$sponsorship->company_name}",
                $action['message'] ?? "The deal with {$sponsorship->company_name} has been updated.",
                $action['notification_type'] ?? Notification::TYPE_INFO,
                "/sponsorships"
            );
        }
    }

    /**
     * Update deal priority
     */
    protected function updatePriority(array $action, Sponsorship $sponsorship): void
    {
        $newPriority = $action['new_priority'] ?? 'Hot';
        $sponsorship->update(['priority' => $newPriority]);
    }

    /**
     * Send email notification (placeholder - would integrate with mail service)
     */
    protected function sendEmail(array $action, Sponsorship $sponsorship): void
    {
        // In a real implementation, this would send an actual email
        // For now, we'll create a notification as a placeholder
        $userId = auth()->id() ?? 1;
        $subject = $action['subject'] ?? 'Workflow Notification';
        $to = $action['to'] ?? $sponsorship->decision_maker_email;
        
        Notification::send(
            $userId,
            "Email Sent: {$subject}",
            "Email would be sent to: {$to}",
            Notification::TYPE_SUCCESS,
            "/sponsorships"
        );
    }

    /**
     * Check for stagnant deals and trigger workflows
     */
    public function checkStagnantDeals(): void
    {
        $stagnantDeals = Sponsorship::whereNotIn('stage', ['Closed Won', 'Closed Lost'])
            ->where('last_activity_at', '<', now()->subDays(14))
            ->get();

        foreach ($stagnantDeals as $deal) {
            $this->executeTrigger(Workflow::TRIGGER_DEAL_STAGNANT, $deal);
        }
    }

    /**
     * Handle stage change and create appropriate tasks
     */
    public function handleStageChange(Sponsorship $sponsorship, string $oldStage, string $newStage): void
    {
        // 4.1 Initial Outreach Task
        if ($newStage === 'Initial Outreach') {
            $this->createInitialOutreachTask($sponsorship);
        }

        // 4.3 Contract Review Task
        if ($newStage === 'Contract & Commitment') {
            $this->createContractReviewTask($sponsorship);
        }
    }

    /**
     * 4.1 Create Initial Outreach Task
     */
    public function createInitialOutreachTask(Sponsorship $sponsorship): void
    {
        Task::create([
            'sponsorship_id' => $sponsorship->id,
            'owner_id' => $sponsorship->user_id,
            'created_by' => auth()->id() ?? $sponsorship->user_id,
            'title' => 'Send introduction email',
            'description' => "Send initial outreach email to {$sponsorship->company_name}",
            'due_date' => now()->addDay(),
            'priority' => Task::PRIORITY_HIGH,
            'status' => Task::STATUS_PENDING,
            'is_automated' => true,
            'automation_type' => 'initial_outreach',
        ]);
    }

    /**
     * 4.2 Create Proposal Follow-up Task
     */
    public function createProposalFollowupTask(Sponsorship $sponsorship): void
    {
        // Check if task already created
        if ($sponsorship->proposal_followup_task_created) {
            return;
        }

        Task::create([
            'sponsorship_id' => $sponsorship->id,
            'owner_id' => $sponsorship->user_id,
            'created_by' => 1, // System user
            'title' => 'Follow up on proposal',
            'description' => "Follow up with {$sponsorship->company_name} regarding the proposal sent on {$sponsorship->proposal_sent_date->format('M d, Y')}",
            'due_date' => now(),
            'priority' => Task::PRIORITY_HIGH,
            'status' => Task::STATUS_PENDING,
            'is_automated' => true,
            'automation_type' => 'proposal_followup',
        ]);

        // Mark as created
        $sponsorship->update(['proposal_followup_task_created' => true]);
    }

    /**
     * 4.3 Create Contract Review Task
     */
    public function createContractReviewTask(Sponsorship $sponsorship): void
    {
        // Try to assign to legal/review user, fallback to owner
        $assignTo = $sponsorship->user_id;

        Task::create([
            'sponsorship_id' => $sponsorship->id,
            'owner_id' => $assignTo,
            'created_by' => auth()->id() ?? $sponsorship->user_id,
            'title' => 'Review contract',
            'description' => "Review and finalize contract for {$sponsorship->company_name}",
            'due_date' => now()->addDays(2),
            'priority' => Task::PRIORITY_HIGH,
            'status' => Task::STATUS_PENDING,
            'is_automated' => true,
            'automation_type' => 'contract_review',
        ]);
    }

    /**
     * 4.4 Create Welcome Package Task
     */
    public function createWelcomePackageTask(Sponsorship $sponsorship): void
    {
        // Get onboarding user or fallback to owner
        $assignTo = $sponsorship->user_id;

        Task::create([
            'sponsorship_id' => $sponsorship->id,
            'owner_id' => $assignTo,
            'created_by' => 1, // System user
            'title' => 'Send welcome package',
            'description' => "Send welcome package and onboarding materials to {$sponsorship->company_name}",
            'due_date' => now()->addDays(3),
            'priority' => Task::PRIORITY_MEDIUM,
            'status' => Task::STATUS_PENDING,
            'is_automated' => true,
            'automation_type' => 'welcome_package',
        ]);
    }
}
