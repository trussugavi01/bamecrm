<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Settings - B.A.M.E CRM')]
class Index extends Component
{
    public $activeTab = 'profile';
    
    // Profile Settings
    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    
    // System Settings
    public $quarterly_goal;
    public $currency;
    public $timezone;
    public $date_format;
    
    // API Settings
    public $api_key;
    public $show_api_key = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        
        // Load system settings from env
        $this->quarterly_goal = env('QUARTERLY_GOAL', 5000000);
        $this->currency = 'GBP';
        $this->timezone = config('app.timezone', 'UTC');
        $this->date_format = 'Y-m-d';
        
        // Load API key
        $this->api_key = env('API_KEY_SALT', '');
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        session()->flash('success', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', 'Password updated successfully!');
    }

    public function regenerateApiKey()
    {
        $newKey = 'bame_live_' . bin2hex(random_bytes(20));
        $this->api_key = $newKey;
        
        session()->flash('success', 'API Key regenerated! Please update your .env file with: API_KEY_SALT=' . $newKey);
    }

    public function toggleApiKeyVisibility()
    {
        $this->show_api_key = !$this->show_api_key;
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
