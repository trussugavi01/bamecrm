<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';
    public string $message = '';
    public string $messageType = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        try {
            $status = Password::sendResetLink(
                ['email' => $this->email]
            );

            if ($status === Password::RESET_LINK_SENT) {
                $this->message = 'Password reset link sent! Please check your email.';
                $this->messageType = 'success';
                $this->email = '';
            } else {
                $this->message = 'Unable to send reset link. Please verify your email address.';
                $this->messageType = 'error';
            }
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage(), [
                'email' => $this->email,
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->message = 'An error occurred while sending the reset link. Please contact support if this persists.';
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.guest');
    }
}
