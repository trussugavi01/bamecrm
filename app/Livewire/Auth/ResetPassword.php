<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $message = '';
    public string $messageType = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'token' => 'required',
    ];

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Password reset successfully! You can now login.');
            return redirect()->route('login');
        } else {
            $this->message = 'Unable to reset password. The reset link may have expired.';
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('components.layouts.guest');
    }
}
