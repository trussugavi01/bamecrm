<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsorship extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'pipeline_id',
        'company_name',
        'decision_maker_name',
        'decision_maker_email',
        'tier',
        'value',
        'currency',
        'stage',
        'stage_entry_date',
        'days_in_stage',
        'probability',
        'priority',
        'source',
        'proposal_sent_date',
        'proposal_followup_task_created',
        'contract_signed_date',
        'actual_close_date',
        'next_follow_up_date',
        'last_activity_at',
        'last_activity_date',
        'loss_reason',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'integer',
        'days_in_stage' => 'integer',
        'proposal_sent_date' => 'date',
        'proposal_followup_task_created' => 'boolean',
        'contract_signed_date' => 'date',
        'actual_close_date' => 'date',
        'next_follow_up_date' => 'date',
        'stage_entry_date' => 'date',
        'last_activity_at' => 'datetime',
        'last_activity_date' => 'date',
    ];

    // Stage to Probability mapping as per PRD
    const STAGE_PROBABILITY_MAP = [
        'Prospect Identification' => 10,
        'Initial Outreach' => 20,
        'Qualification & Discovery' => 35,
        'Proposal Development' => 50,
        'Negotiation' => 70,
        'Contract & Commitment' => 90,
        'Active Partnership' => 100,
        'Closed Lost' => 0,
    ];

    const STAGES = [
        'Prospect Identification',
        'Initial Outreach',
        'Qualification & Discovery',
        'Proposal Development',
        'Negotiation',
        'Contract & Commitment',
        'Active Partnership',
        'Closed Lost',
    ];

    const TIERS = ['Platinum', 'Gold', 'Silver', 'Bronze', 'In-Kind'];
    const PRIORITIES = ['Hot', 'Warm', 'Cold'];
    const SOURCES = ['Web Form', 'Referral', 'Outreach', 'Event', 'API'];

    protected static function booted()
    {
        // Auto-update probability when stage changes
        static::saving(function ($sponsorship) {
            if ($sponsorship->isDirty('stage')) {
                $sponsorship->probability = self::STAGE_PROBABILITY_MAP[$sponsorship->stage] ?? 10;
            }

            // Update last_activity_at on any change
            if ($sponsorship->isDirty() && !$sponsorship->wasRecentlyCreated) {
                $sponsorship->last_activity_at = now();
            }

            // Set close date when moving to Active Partnership
            if ($sponsorship->stage === 'Active Partnership' && !$sponsorship->actual_close_date) {
                $sponsorship->actual_close_date = now();
            }
        });
    }

    /**
     * Get the user that owns the sponsorship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pipeline that owns the sponsorship.
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    /**
     * Get the activities for the sponsorship.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the tasks for the sponsorship.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the logs for the sponsorship.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(SponsorshipLog::class);
    }

    /**
     * Check if the deal is stagnant (no activity in 14 days).
     */
    public function isStagnant(): bool
    {
        return $this->last_activity_at->lt(now()->subDays(14));
    }

    /**
     * Get weighted value (value * probability).
     */
    public function getWeightedValueAttribute(): float
    {
        return ($this->value * $this->probability) / 100;
    }

    /**
     * Validate stage transition rules.
     */
    public function canMoveToStage(string $newStage): array
    {
        $errors = [];

        // Proposal Gate: Cannot move to Negotiation without proposal_sent_date
        if ($newStage === 'Negotiation' && !$this->proposal_sent_date) {
            $errors[] = 'Cannot move to Negotiation without sending a proposal first.';
        }

        // Win Gate: Cannot move to Active Partnership without contract_signed_date
        if ($newStage === 'Active Partnership' && !$this->contract_signed_date) {
            $errors[] = 'Cannot move to Active Partnership without a signed contract.';
        }

        // Loss Gate: Cannot move to Closed Lost without loss_reason
        if ($newStage === 'Closed Lost' && !$this->loss_reason) {
            $errors[] = 'Cannot mark as Closed Lost without providing a loss reason.';
        }

        return $errors;
    }

    /**
     * Log an activity for this sponsorship.
     */
    public function logActivity(string $type, string $description, ?int $userId = null): void
    {
        $this->activities()->create([
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'description' => $description,
        ]);

        $this->touch('last_activity_at');
    }
}
