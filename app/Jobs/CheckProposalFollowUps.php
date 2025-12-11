<?php

namespace App\Jobs;

use App\Models\Sponsorship;
use App\Services\WorkflowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckProposalFollowUps implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected $workflowService;

    public function __construct()
    {
        $this->workflowService = app(WorkflowService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('CheckProposalFollowUps job started');

        $processedCount = 0;
        $tasksCreated = 0;

        // 4.2 Proposal Follow-up
        // Find opportunities where proposal was sent 3+ days ago and no follow-up task created
        $opportunities = Sponsorship::query()
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->whereNotNull('proposal_sent_date')
            ->where('proposal_sent_date', '<=', now()->subDays(3)->toDateString())
            ->where('proposal_followup_task_created', false)
            ->get();

        foreach ($opportunities as $opportunity) {
            try {
                $processedCount++;

                // Create follow-up task
                $this->workflowService->createProposalFollowupTask($opportunity);
                $tasksCreated++;

            } catch (\Exception $e) {
                Log::error('Failed to create proposal follow-up task', [
                    'opportunity_id' => $opportunity->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('CheckProposalFollowUps job completed', [
            'processed' => $processedCount,
            'tasks_created' => $tasksCreated,
        ]);
    }
}
