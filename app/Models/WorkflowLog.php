<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowLog extends Model
{
    protected $fillable = [
        'workflow_id',
        'sponsorship_id',
        'status',
        'message',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_SKIPPED = 'skipped';

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }
}
