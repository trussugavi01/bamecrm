<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class OverdueTasksDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tasks;

    public function __construct(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Overdue Tasks Digest - ' . $this->tasks->count() . ' task(s)')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have **' . $this->tasks->count() . ' overdue task(s)** that need your attention:')
            ->line('');

        foreach ($this->tasks->take(10) as $task) {
            $daysOverdue = now()->diffInDays($task->due_date);
            $opportunityName = $task->sponsorship ? $task->sponsorship->company_name : 'N/A';
            
            $message->line('**' . $task->title . '**')
                ->line('• Opportunity: ' . $opportunityName)
                ->line('• Due: ' . $task->due_date->format('M d, Y') . ' (' . $daysOverdue . ' days overdue)')
                ->line('• Priority: ' . ucfirst($task->priority))
                ->line('');
        }

        if ($this->tasks->count() > 10) {
            $message->line('... and ' . ($this->tasks->count() - 10) . ' more task(s)');
        }

        return $message
            ->action('View All Tasks', url('/tasks'))
            ->line('Please update these tasks to keep your pipeline on track.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'overdue_count' => $this->tasks->count(),
            'tasks' => $this->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date->toDateString(),
                    'sponsorship' => $task->sponsorship ? $task->sponsorship->company_name : null,
                    'days_overdue' => now()->diffInDays($task->due_date),
                ];
            })->toArray(),
            'message' => 'You have ' . $this->tasks->count() . ' overdue task(s)',
        ];
    }
}
