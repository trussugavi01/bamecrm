<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\OverdueTasksDigestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOverdueTasksDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendOverdueTasksDigest job started');

        $processedCount = 0;
        $notifiedCount = 0;

        // 3.5 Overdue Task Daily Digest
        // Get all users with overdue tasks
        $usersWithOverdueTasks = User::whereHas('tasks', function ($query) {
            $query->where('due_date', '<', now()->toDateString())
                ->where('status', '!=', Task::STATUS_COMPLETED)
                ->where('status', '!=', Task::STATUS_CANCELLED);
        })->get();

        foreach ($usersWithOverdueTasks as $user) {
            try {
                $processedCount++;

                // Get user's overdue tasks
                $overdueTasks = Task::where('owner_id', $user->id)
                    ->where('due_date', '<', now()->toDateString())
                    ->whereNotIn('status', [Task::STATUS_COMPLETED, Task::STATUS_CANCELLED])
                    ->with('sponsorship')
                    ->orderBy('due_date')
                    ->get();

                if ($overdueTasks->count() > 0) {
                    $user->notify(new OverdueTasksDigestNotification($overdueTasks));
                    $notifiedCount++;
                }

            } catch (\Exception $e) {
                Log::error('Failed to send overdue tasks digest', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('SendOverdueTasksDigest job completed', [
            'processed' => $processedCount,
            'notified' => $notifiedCount,
        ]);
    }
}
