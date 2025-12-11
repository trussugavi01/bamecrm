<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'trigger_type',
        'trigger_config',
        'actions',
        'is_active',
        'execution_count',
        'last_executed_at',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
    ];

    // Trigger types
    const TRIGGER_STAGE_CHANGE = 'stage_change';
    const TRIGGER_DEAL_STAGNANT = 'deal_stagnant';
    const TRIGGER_DEAL_CREATED = 'deal_created';
    const TRIGGER_DEAL_WON = 'deal_won';
    const TRIGGER_DEAL_LOST = 'deal_lost';
    const TRIGGER_FOLLOW_UP_DUE = 'follow_up_due';

    const TRIGGERS = [
        self::TRIGGER_STAGE_CHANGE => 'When deal moves to a stage',
        self::TRIGGER_DEAL_STAGNANT => 'When deal becomes stagnant (14+ days inactive)',
        self::TRIGGER_DEAL_CREATED => 'When new deal is created',
        self::TRIGGER_DEAL_WON => 'When deal is won (Closed Won)',
        self::TRIGGER_DEAL_LOST => 'When deal is lost (Closed Lost)',
        self::TRIGGER_FOLLOW_UP_DUE => 'When follow-up is due',
    ];

    // Action types
    const ACTION_SEND_EMAIL = 'send_email';
    const ACTION_CREATE_TASK = 'create_task';
    const ACTION_UPDATE_PRIORITY = 'update_priority';
    const ACTION_NOTIFY_USER = 'notify_user';
    const ACTION_NOTIFY_TEAM = 'notify_team';

    const ACTIONS_LIST = [
        self::ACTION_SEND_EMAIL => 'Send Email Notification',
        self::ACTION_CREATE_TASK => 'Create Follow-up Task',
        self::ACTION_UPDATE_PRIORITY => 'Update Deal Priority',
        self::ACTION_NOTIFY_USER => 'Send In-App Notification',
        self::ACTION_NOTIFY_TEAM => 'Notify Entire Team',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getTriggerLabelAttribute(): string
    {
        return self::TRIGGERS[$this->trigger_type] ?? $this->trigger_type;
    }

    public function incrementExecutionCount(): void
    {
        $this->increment('execution_count');
        $this->update(['last_executed_at' => now()]);
    }
}
