<?php

namespace App\Notifications;

use App\Models\Sponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StageProgressionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sponsorship;
    protected $newStage;

    public function __construct(Sponsorship $sponsorship, string $newStage)
    {
        $this->sponsorship = $sponsorship;
        $this->newStage = $newStage;
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
            ->subject('Deal Stage Update - ' . $this->sponsorship->company_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The opportunity with **' . $this->sponsorship->company_name . '** has progressed to **' . $this->newStage . '**.')
            ->line('**Deal Summary:**')
            ->line('• Owner: ' . $this->sponsorship->user->name)
            ->line('• Value: £' . number_format($this->sponsorship->value, 2))
            ->line('• Probability: ' . $this->sponsorship->probability . '%')
            ->line('• Tier: ' . $this->sponsorship->tier);

        // Add stage-specific next steps
        $nextSteps = $this->getNextSteps($this->newStage);
        if ($nextSteps) {
            $message->line('')
                ->line('**Next Required Actions:**')
                ->line($nextSteps);
        }

        return $message
            ->action('View Opportunity', url('/sponsorships/' . $this->sponsorship->id))
            ->line('Keep up the great work!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'sponsorship_id' => $this->sponsorship->id,
            'company_name' => $this->sponsorship->company_name,
            'new_stage' => $this->newStage,
            'value' => $this->sponsorship->value,
            'probability' => $this->sponsorship->probability,
            'owner' => $this->sponsorship->user->name,
            'message' => $this->sponsorship->company_name . ' moved to ' . $this->newStage,
        ];
    }

    /**
     * Get next steps based on stage.
     */
    protected function getNextSteps(string $stage): ?string
    {
        $steps = [
            'Negotiation' => '• Review pricing and terms\n• Prepare for negotiations\n• Coordinate with sales team',
            'Contract & Commitment' => '• Review contract terms\n• Coordinate with legal team\n• Prepare for signing',
        ];

        return $steps[$stage] ?? null;
    }
}
