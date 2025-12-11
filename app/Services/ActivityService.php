<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    /**
     * Log an activity for a sponsorship and update last_activity_date.
     * 
     * @param int $sponsorshipId
     * @param string $type (call, email, meeting, note, task_update, etc.)
     * @param array $payload Additional data about the activity
     * @return Activity
     */
    public function logActivity(int $sponsorshipId, string $type, array $payload = []): Activity
    {
        $sponsorship = Sponsorship::findOrFail($sponsorshipId);

        // Create activity record
        $activity = Activity::create([
            'user_id' => Auth::id() ?? $sponsorship->user_id,
            'sponsorship_id' => $sponsorshipId,
            'type' => $type,
            'description' => $payload['description'] ?? $this->getDefaultDescription($type),
            'metadata' => $payload['metadata'] ?? null,
            'created_at' => now(),
        ]);

        // Update last_activity_date on sponsorship (2.2 Last Activity Date)
        $sponsorship->update([
            'last_activity_date' => now()->toDateString(),
            'last_activity_at' => now(),
        ]);

        return $activity;
    }

    /**
     * Log a call activity.
     */
    public function logCall(int $sponsorshipId, string $notes, ?int $duration = null): Activity
    {
        return $this->logActivity($sponsorshipId, 'call', [
            'description' => 'Phone call logged',
            'metadata' => [
                'notes' => $notes,
                'duration_minutes' => $duration,
            ],
        ]);
    }

    /**
     * Log an email activity.
     */
    public function logEmail(int $sponsorshipId, string $subject, string $direction = 'sent'): Activity
    {
        return $this->logActivity($sponsorshipId, 'email', [
            'description' => "Email {$direction}: {$subject}",
            'metadata' => [
                'subject' => $subject,
                'direction' => $direction,
            ],
        ]);
    }

    /**
     * Log a meeting activity.
     */
    public function logMeeting(int $sponsorshipId, string $title, ?\DateTime $meetingDate = null): Activity
    {
        return $this->logActivity($sponsorshipId, 'meeting', [
            'description' => "Meeting: {$title}",
            'metadata' => [
                'title' => $title,
                'meeting_date' => $meetingDate ? $meetingDate->format('Y-m-d H:i:s') : now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Log a note activity.
     */
    public function logNote(int $sponsorshipId, string $note): Activity
    {
        return $this->logActivity($sponsorshipId, 'note', [
            'description' => 'Note added',
            'metadata' => [
                'note' => $note,
            ],
        ]);
    }

    /**
     * Log a task update activity.
     */
    public function logTaskUpdate(int $sponsorshipId, string $taskTitle, string $action): Activity
    {
        return $this->logActivity($sponsorshipId, 'task_update', [
            'description' => "Task {$action}: {$taskTitle}",
            'metadata' => [
                'task_title' => $taskTitle,
                'action' => $action,
            ],
        ]);
    }

    /**
     * Get default description based on activity type.
     */
    protected function getDefaultDescription(string $type): string
    {
        $descriptions = [
            'call' => 'Phone call logged',
            'email' => 'Email activity',
            'meeting' => 'Meeting logged',
            'note' => 'Note added',
            'task_update' => 'Task updated',
            'file_upload' => 'File uploaded',
            'status_change' => 'Status changed',
        ];

        return $descriptions[$type] ?? 'Activity logged';
    }

    /**
     * Get recent activities for a sponsorship.
     */
    public function getRecentActivities(int $sponsorshipId, int $limit = 10)
    {
        return Activity::where('sponsorship_id', $sponsorshipId)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity timeline for a sponsorship.
     */
    public function getActivityTimeline(int $sponsorshipId)
    {
        return Activity::where('sponsorship_id', $sponsorshipId)
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            });
    }
}
