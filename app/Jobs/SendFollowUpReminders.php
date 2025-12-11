<?php

namespace App\Jobs;

use App\Models\Sponsorship;
use App\Notifications\FollowUpReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFollowUpReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendFollowUpReminders job started');

        $processedCount = 0;
        $notifiedCount = 0;

        // 3.2 Follow-up Reminder
        // Find opportunities with follow-up date tomorrow
        $opportunities = Sponsorship::query()
            ->whereNotIn('stage', ['Active Partnership', 'Closed Lost'])
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', now()->addDay()->toDateString())
            ->with('user')
            ->get();

        foreach ($opportunities as $opportunity) {
            try {
                $processedCount++;

                // Send notification to owner
                if ($opportunity->user) {
                    $opportunity->user->notify(new FollowUpReminderNotification($opportunity));
                    $notifiedCount++;
                }

            } catch (\Exception $e) {
                Log::error('Failed to send follow-up reminder', [
                    'opportunity_id' => $opportunity->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('SendFollowUpReminders job completed', [
            'processed' => $processedCount,
            'notified' => $notifiedCount,
        ]);
    }
}
