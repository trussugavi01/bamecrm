<?php

namespace App\Jobs;

use App\Models\Sponsorship;
use App\Notifications\StagnantOpportunityNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckStagnantOpportunities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('CheckStagnantOpportunities job started');

        $processedCount = 0;
        $notifiedCount = 0;

        // 3.1 Stagnant Opportunity Alert
        // Find opportunities with no activity in 14 days, not closed
        $stagnantOpportunities = Sponsorship::query()
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->where(function ($query) {
                $query->whereNull('last_activity_date')
                    ->orWhere('last_activity_date', '<=', now()->subDays(14)->toDateString());
            })
            ->with('user')
            ->get();

        foreach ($stagnantOpportunities as $opportunity) {
            try {
                $processedCount++;

                // Send notification to owner
                if ($opportunity->user) {
                    $opportunity->user->notify(new StagnantOpportunityNotification($opportunity));
                    $notifiedCount++;
                }

            } catch (\Exception $e) {
                Log::error('Failed to process stagnant opportunity', [
                    'opportunity_id' => $opportunity->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('CheckStagnantOpportunities job completed', [
            'processed' => $processedCount,
            'notified' => $notifiedCount,
        ]);
    }
}
