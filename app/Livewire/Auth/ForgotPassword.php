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
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.guest');
    }
}
