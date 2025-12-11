<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'sponsorship_id',
        'user_id',
        'type',
        'description',
    ];

    const TYPE_STAGE_CHANGE = 'stage_change';
    const TYPE_NOTE = 'note';
    const TYPE_EMAIL = 'email';
    const TYPE_TASK_COMPLETE = 'task_complete';
    const TYPE_FORM_SUBMISSION = 'form_submission';

    /**
     * Get the sponsorship that owns the activity.
     */
    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    /**
     * Get the user that created the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
