<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorshipLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sponsorship_id',
        'user_id',
        'event_type',
        'old_value',
        'new_value',
        'description',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the sponsorship that owns the log.
     */
    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
