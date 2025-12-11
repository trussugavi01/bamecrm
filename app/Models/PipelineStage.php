<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineStage extends Model
{
    protected $fillable = [
        'pipeline_id',
        'name',
        'probability',
        'order',
        'color',
    ];

    protected $casts = [
        'probability' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the pipeline that owns the stage.
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
}
