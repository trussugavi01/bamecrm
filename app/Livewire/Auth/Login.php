<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.guest')]
#[Title('Login - B.A.M.E CRM')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $showPassword = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
