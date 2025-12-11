<?php

namespace App\Notifications;

use App\Models\Sponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sponsorship;

    public function __construct(Sponsorship $sponsorship)
    {
        $this->sponsorship = $sponsorship;
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
        return (new MailMessage)
            ->subject('Follow-up Reminder - ' . $this->sponsorship->company_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that you have a follow-up scheduled for tomorrow with **' . $this->sponsorship->company_name . '**.')
            ->line('**Deal Details:**')
            ->line('• Value: £' . number_format($this->sponsorship->value, 2))
            ->line('• Stage: ' . $this->sponsorship->stage)
            ->line('• Tier: ' . $this->sponsorship->tier)
            ->line('• Follow-up Date: ' . $this->sponsorship->next_follow_up_date->format('M d, Y'))
            ->action('View Opportunity', url('/sponsorships/' . $this->sponsorship->id))
            ->line('Good luck with your follow-up!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'sponsorship_id' => $this->sponsorship->id,
            'company_name' => $this->sponsorship->company_name,
            'stage' => $this->sponsorship->stage,
            'value' => $this->sponsorship->value,
            'follow_up_date' => $this->sponsorship->next_follow_up_date->toDateString(),
            'message' => 'Follow-up reminder for ' . $this->sponsorship->company_name,
        ];
    }
}
