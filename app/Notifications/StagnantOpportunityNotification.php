<?php

namespace App\Notifications;

use App\Models\Sponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StagnantOpportunityNotification extends Notification implements ShouldQueue
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
        $daysSinceActivity = $this->sponsorship->last_activity_date 
            ? now()->diffInDays($this->sponsorship->last_activity_date)
            : 'more than 14';

        return (new MailMessage)
            ->subject('Stagnant Opportunity Alert - ' . $this->sponsorship->company_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The opportunity with **' . $this->sponsorship->company_name . '** has had no activity for ' . $daysSinceActivity . ' days.')
            ->line('**Deal Details:**')
            ->line('• Value: £' . number_format($this->sponsorship->value, 2))
            ->line('• Stage: ' . $this->sponsorship->stage)
            ->line('• Tier: ' . $this->sponsorship->tier)
            ->line('• Priority: ' . $this->sponsorship->priority)
            ->line('')
            ->line('**Recommended Actions:**')
            ->line('• Schedule a follow-up call or meeting')
            ->line('• Send a check-in email')
            ->line('• Update the deal status or notes')
            ->action('View Opportunity', url('/sponsorships/' . $this->sponsorship->id))
            ->line('Keep your pipeline moving forward!');
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
            'days_since_activity' => $this->sponsorship->last_activity_date 
                ? now()->diffInDays($this->sponsorship->last_activity_date)
                : null,
            'message' => 'Opportunity with ' . $this->sponsorship->company_name . ' has been stagnant',
        ];
    }
}
