<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the sponsorships for the user.
     */
    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    /**
     * Get the lead forms for the user.
     */
    public function leadForms()
    {
        return $this->hasMany(LeadForm::class);
    }

    /**
     * Get the activities for the user.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the tasks owned by the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'owner_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is consultant.
     */
    public function isConsultant(): bool
    {
        return $this->role === 'consultant';
    }

    /**
     * Check if user is executive.
     */
    public function isExecutive(): bool
    {
        return $this->role === 'executive';
    }

    /**
     * Check if user is approver.
     */
    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
