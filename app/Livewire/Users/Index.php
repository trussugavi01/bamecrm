<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
#[Title('User Management - B.A.M.E CRM')]
class Index extends Component
{
    public $users;
    public $showModal = false;
    public $editingUserId = null;
    public $search = '';
    public $roleFilter = '';

    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'consultant';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:6',
        'role' => 'required|in:admin,consultant,executive,approver',
    ];

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $this->users = $query->latest()->get();
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function updatedRoleFilter()
    {
        $this->loadUsers();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'password', 'role', 'editingUserId']);
        $this->role = 'consultant';
        $this->showModal = true;
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $this->editingUserId = $userId;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->showModal = true;
    }

    public function saveUser()
    {
        $rules = $this->rules;
        
        if ($this->editingUserId) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->editingUserId;
            $rules['password'] = 'nullable|string|min:6';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,email';
            $rules['password'] = 'required|string|min:6';
        }

        $this->validate($rules);

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->role = $this->role;
            
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            
            $user->save();
            
            session()->flash('success', 'User updated successfully!');
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);
            
            session()->flash('success', 'User created successfully!');
        }

        $this->showModal = false;
        $this->loadUsers();
    }

    public function deleteUser($userId)
    {
        // Prevent deleting yourself
        if ($userId == auth()->id()) {
            session()->flash('error', 'You cannot delete your own account!');
            return;
        }

        User::findOrFail($userId)->delete();
        session()->flash('success', 'User deleted successfully!');
        $this->loadUsers();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.users.index');
    }
}
