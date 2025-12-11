<?php

namespace App\Observers;

use App\Models\Sponsorship;
use App\Models\SponsorshipLog;
use App\Services\WorkflowService;
use App\Notifications\StageProgressionNotification;
use App\Notifications\WonOpportunityNotification;
use Illuminate\Support\Facades\Notification;

class SponsorshipObserver
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Handle the Sponsorship "creating" event.
     */
    public function creating(Sponsorship $sponsorship): void
    {
        // Set initial stage entry date
        if (!$sponsorship->stage_entry_date) {
            $sponsorship->stage_entry_date = now();
        }

        // Set initial probability based on stage
        if (!$sponsorship->probability && $sponsorship->stage) {
            $sponsorship->probability = Sponsorship::STAGE_PROBABILITY_MAP[$sponsorship->stage] ?? 10;
        }

        // Set initial last activity date
        if (!$sponsorship->last_activity_date) {
            $sponsorship->last_activity_date = now()->toDateString();
        }
    }

    /**
     * Handle the Sponsorship "updating" event.
     */
    public function updating(Sponsorship $sponsorship): void
    {
        $original = $sponsorship->getOriginal();

        // 2.1 Probability by Stage - Auto-update probability when stage changes
        if ($sponsorship->isDirty('stage')) {
            $oldStage = $original['stage'];
            $newStage = $sponsorship->stage;

            // Update probability based on new stage
            $sponsorship->probability = Sponsorship::STAGE_PROBABILITY_MAP[$newStage] ?? 10;

            // Update stage entry date
            $sponsorship->stage_entry_date = now();
            $sponsorship->days_in_stage = 0;

            // 2.4 Actual Close Date - Set when moving to closed stages
            if (in_array($newStage, ['Active Partnership', 'Closed Lost']) && !$sponsorship->actual_close_date) {
                $sponsorship->actual_close_date = now();
            }

            // Log stage change
            $this->logChange($sponsorship, 'stage_change', $oldStage, $newStage);

            // Validate stage transition
            $errors = $sponsorship->canMoveToStage($newStage);
            if (!empty($errors)) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    response()->json(['errors' => ['stage' => $errors]], 422)
                );
            }
        }

        // Update last activity date on any field change
        if ($sponsorship->isDirty() && !$sponsorship->isDirty('last_activity_date')) {
            $sponsorship->last_activity_date = now()->toDateString();
        }
    }

    /**
     * Handle the Sponsorship "updated" event.
     */
    public function updated(Sponsorship $sponsorship): void
    {
        $original = $sponsorship->getOriginal();

        // Handle stage change workflows
        if ($sponsorship->wasChanged('stage')) {
            $newStage = $sponsorship->stage;

            // Create automated tasks based on stage
            $this->workflowService->handleStageChange($sponsorship, $original['stage'], $newStage);

            // Send stage progression notifications
            $this->sendStageNotifications($sponsorship, $newStage);

            // Handle won opportunity
            if ($newStage === 'Active Partnership') {
                $this->handleWonOpportunity($sponsorship);
            }
        }

        // Log other significant changes
        foreach (['value', 'tier', 'priority', 'probability'] as $field) {
            if ($sponsorship->wasChanged($field)) {
                $this->logChange($sponsorship, 'field_update', $original[$field], $sponsorship->$field, $field);
            }
        }
    }

    /**
     * Send notifications based on stage progression.
     */
    protected function sendStageNotifications(Sponsorship $sponsorship, string $newStage): void
    {
        $notifyUsers = [];

        // Negotiation stage - notify sales team and Hap Consulting
        if ($newStage === 'Negotiation') {
            // Get sales team users (you can customize this query)
            $notifyUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['sales', 'admin']);
            })->get();
        }

        // Contract & Commitment - notify leadership and finance
        if ($newStage === 'Contract & Commitment') {
            $notifyUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['leadership', 'finance', 'admin']);
            })->get();
        }

        if (!empty($notifyUsers)) {
            Notification::send($notifyUsers, new StageProgressionNotification($sponsorship, $newStage));
        }

        // Always notify the owner
        if ($sponsorship->user) {
            $sponsorship->user->notify(new StageProgressionNotification($sponsorship, $newStage));
        }
    }

    /**
     * Handle won opportunity workflows.
     */
    protected function handleWonOpportunity(Sponsorship $sponsorship): void
    {
        // Send won opportunity notification
        $notifyUsers = \App\Models\User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['leadership', 'finance', 'admin']);
        })->get();

        Notification::send($notifyUsers, new WonOpportunityNotification($sponsorship));

        if ($sponsorship->user) {
            $sponsorship->user->notify(new WonOpportunityNotification($sponsorship));
        }

        // Create onboarding task
        $this->workflowService->createWelcomePackageTask($sponsorship);
    }

    /**
     * Log a change to the sponsorship.
     */
    protected function logChange(Sponsorship $sponsorship, string $eventType, $oldValue, $newValue, ?string $field = null): void
    {
        SponsorshipLog::create([
            'sponsorship_id' => $sponsorship->id,
            'user_id' => auth()->id(),
            'event_type' => $eventType,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $field ? "Changed {$field} from {$oldValue} to {$newValue}" : "Stage changed from {$oldValue} to {$newValue}",
            'metadata' => $field ? ['field' => $field] : null,
            'created_at' => now(),
        ]);
    }
}
