<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadForm extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'form_schema',
        'submit_button_text',
        'success_message',
        'redirect_url',
    ];

    protected $casts = [
        'form_schema' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($leadForm) {
            if (!$leadForm->uuid) {
                $leadForm->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the lead form.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the public URL for this form.
     */
    public function getPublicUrlAttribute(): string
    {
        return url("/f/{$this->uuid}");
    }

    /**
     * Get the embed code for this form.
     */
    public function getEmbedCodeAttribute(): string
    {
        return '<iframe frameborder="0" height="600" src="' . $this->public_url . '" width="100%"></iframe>';
    }

    /**
     * Get default form schema.
     */
    public static function defaultSchema(): array
    {
        return [
            'company' => ['visible' => true, 'required' => true, 'label' => 'Company'],
            'name' => ['visible' => true, 'required' => true, 'label' => 'Full Name'],
            'email' => ['visible' => true, 'required' => true, 'label' => 'Email'],
            'tier' => ['visible' => true, 'required' => false, 'label' => 'Sponsorship Tier'],
            'message' => ['visible' => true, 'required' => false, 'label' => 'Message'],
        ];
    }
}
