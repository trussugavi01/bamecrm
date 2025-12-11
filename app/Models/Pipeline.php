<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the pipeline.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the stages for the pipeline.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class)->orderBy('order');
    }

    /**
     * Get the sponsorships using this pipeline.
     */
    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    /**
     * Get default pipeline stages schema.
     */
    public static function defaultStages(): array
    {
        return [
            ['name' => 'Prospect Identification', 'probability' => 10, 'order' => 1, 'color' => '#3B82F6'],
            ['name' => 'Initial Outreach', 'probability' => 20, 'order' => 2, 'color' => '#8B5CF6'],
            ['name' => 'Qualification & Discovery', 'probability' => 35, 'order' => 3, 'color' => '#EC4899'],
            ['name' => 'Proposal Development', 'probability' => 50, 'order' => 4, 'color' => '#F59E0B'],
            ['name' => 'Negotiation', 'probability' => 70, 'order' => 5, 'color' => '#10B981'],
            ['name' => 'Contract & Commitment', 'probability' => 90, 'order' => 6, 'color' => '#059669'],
        ];
    }
}
