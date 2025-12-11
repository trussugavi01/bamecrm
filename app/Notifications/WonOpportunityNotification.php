<?php

namespace App\Notifications;

use App\Models\Sponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WonOpportunityNotification extends Notification implements ShouldQueue
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
            ->subject('🎉 Deal Won - ' . $this->sponsorship->company_name)
            ->greeting('Congratulations!!')
            ->line('We have successfully closed a deal with **' . $this->sponsorship->company_name . '**!')
            ->line('')
            ->line('**Deal Details:**')
            ->line('• Opportunity ID: #' . $this->sponsorship->id)
            ->line('• Sponsor Name: ' . $this->sponsorship->company_name)
            ->line('• Value: £' . number_format($this->sponsorship->value, 2))
            ->line('• Tier: ' . $this->sponsorship->tier)
            ->line('• Owner: ' . $this->sponsorship->user->name)
            ->line('• Close Date: ' . ($this->sponsorship->actual_close_date ? $this->sponsorship->actual_close_date->format('M d, Y') : 'Today'))
            ->line('')
            ->line('**Next Steps Checklist:**')
            ->line('✓ Send welcome package')
            ->line('✓ Schedule onboarding call')
            ->line('✓ Set up sponsor portal access')
            ->line('✓ Assign account manager')
            ->line('✓ Update finance system')
            ->action('View Opportunity', url('/sponsorships/' . $this->sponsorship->id))
            ->line('Excellent work closing this deal!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'sponsorship_id' => $this->sponsorship->id,
            'company_name' => $this->sponsorship->company_name,
            'value' => $this->sponsorship->value,
            'tier' => $this->sponsorship->tier,
            'owner' => $this->sponsorship->user->name,
            'close_date' => $this->sponsorship->actual_close_date?->toDateString(),
            'message' => 'Deal won with ' . $this->sponsorship->company_name . ' - £' . number_format($this->sponsorship->value, 2),
        ];
    }
}
