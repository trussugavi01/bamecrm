# Workflow & Automation System - Implementation Guide

## Overview
This document describes the comprehensive workflow and automation system implemented for the B.A.M.E CRM. The system automates field updates, notifications, task creation, and scheduled processes to streamline deal management.

## Table of Contents
1. [Database Schema](#database-schema)
2. [Automated Field Updates](#automated-field-updates)
3. [Automated Notifications](#automated-notifications)
4. [Automated Task Creation](#automated-task-creation)
5. [Validation Rules](#validation-rules)
6. [Scheduled Jobs](#scheduled-jobs)
7. [Setup & Configuration](#setup--configuration)
8. [Testing](#testing)

---

## Database Schema

### New Fields Added to `sponsorships` Table
- `stage_entry_date` (date) - Date when opportunity entered current stage
- `days_in_stage` (integer) - Number of days in current stage
- `last_activity_date` (date) - Date of last activity
- `currency` (string) - Currency code (default: GBP)
- `proposal_followup_task_created` (boolean) - Flag to prevent duplicate tasks

### New Tables Created

#### `tasks` Table
```sql
- id (bigint)
- sponsorship_id (foreign key)
- owner_id (foreign key to users)
- created_by (foreign key to users)
- title (string)
- description (text)
- status (enum: pending, in_progress, completed, cancelled)
- priority (enum: low, medium, high, urgent)
- due_date (date)
- completed_at (datetime)
- is_automated (boolean)
- automation_type (string)
- timestamps
```

#### `sponsorship_logs` Table
```sql
- id (bigint)
- sponsorship_id (foreign key)
- user_id (foreign key)
- event_type (string)
- old_value (string)
- new_value (string)
- description (text)
- metadata (json)
- created_at (timestamp)
```

---

## Automated Field Updates

### 2.1 Probability by Stage
**Trigger:** When `stage` field changes  
**Action:** Automatically updates `probability` based on stage mapping

**Stage-Probability Mapping:**
- Prospect Identification → 10%
- Initial Outreach → 20%
- Qualification & Discovery → 35%
- Proposal Development → 50%
- Negotiation → 70%
- Contract & Commitment → 90%
- Active Partnership → 100%
- Closed Lost → 0%

**Implementation:** `SponsorshipObserver::updating()`

### 2.2 Last Activity Date
**Trigger:** Any field update on sponsorship  
**Action:** Sets `last_activity_date` to current date

**Implementation:** `SponsorshipObserver::updating()`

### 2.3 Days in Current Stage
**Type:** Computed field updated nightly  
**Calculation:** `days_in_stage = today - stage_entry_date`

**Implementation:** `UpdateDaysInStage` job (runs at 02:00 daily)

### 2.4 Actual Close Date
**Trigger:** Stage changes to "Active Partnership" or "Closed Lost"  
**Action:** Sets `actual_close_date` to current date (if not already set)

**Implementation:** `SponsorshipObserver::updating()`

---

## Automated Notifications

All notifications support:
- ✅ Email delivery
- ✅ In-app notifications (database)
- 🔄 WhatsApp (optional, requires integration)

### 3.1 Stagnant Opportunity Alert
**Schedule:** Daily at 08:00  
**Condition:** No activity in 14+ days AND not closed  
**Recipients:** Opportunity owner  
**Content:**
- Days since last activity
- Deal details (value, stage, tier, priority)
- Recommended actions
- Quick link to opportunity

**Implementation:** `CheckStagnantOpportunities` job + `StagnantOpportunityNotification`

### 3.2 Follow-up Reminder
**Schedule:** Daily at 08:00  
**Condition:** `next_follow_up_date` is tomorrow  
**Recipients:** Opportunity owner  
**Content:**
- Follow-up date
- Deal details
- Quick action links

**Implementation:** `SendFollowUpReminders` job + `FollowUpReminderNotification`

### 3.3 Stage Progression Notifications
**Trigger:** Real-time when stage changes  
**Special Stages:**
- **Negotiation** → Notifies sales team + admins
- **Contract & Commitment** → Notifies leadership + finance team

**Content:**
- Opportunity summary
- Owner information
- Value and probability
- Next required actions

**Implementation:** `SponsorshipObserver::updated()` + `StageProgressionNotification`

### 3.4 Won Opportunity Notification
**Trigger:** Real-time when stage = "Active Partnership"  
**Recipients:** Leadership, finance team, account managers  
**Content:**
- 🎉 Celebration message
- Complete deal details
- Next steps checklist:
  - Send welcome package
  - Schedule onboarding call
  - Set up portal access
  - Assign account manager
  - Update finance system

**Implementation:** `SponsorshipObserver::updated()` + `WonOpportunityNotification`

### 3.5 Overdue Tasks Daily Digest
**Schedule:** Daily at 08:00  
**Condition:** Tasks with `due_date < today` AND status != completed  
**Recipients:** Task owners (grouped by user)  
**Content:**
- Count of overdue tasks
- List of tasks with:
  - Task title
  - Linked opportunity
  - Days overdue
  - Priority level

**Implementation:** `SendOverdueTasksDigest` job + `OverdueTasksDigestNotification`

---

## Automated Task Creation

### 4.1 Initial Outreach Task
**Trigger:** Stage changes to "Initial Outreach"  
**Task Details:**
- Title: "Send introduction email"
- Due: Tomorrow (+1 day)
- Priority: High
- Owner: Opportunity owner

**Implementation:** `WorkflowService::createInitialOutreachTask()`

### 4.2 Proposal Follow-up Task
**Trigger:** Scheduled check (daily at 09:00)  
**Condition:** `proposal_sent_date` is 3+ days ago AND no follow-up task created  
**Task Details:**
- Title: "Follow up on proposal"
- Due: Today
- Priority: High
- Owner: Opportunity owner

**Implementation:** `CheckProposalFollowUps` job + `WorkflowService::createProposalFollowupTask()`

### 4.3 Contract Review Task
**Trigger:** Stage changes to "Contract & Commitment"  
**Task Details:**
- Title: "Review contract"
- Due: +2 days
- Priority: High
- Owner: Legal/review user or opportunity owner

**Implementation:** `WorkflowService::createContractReviewTask()`

### 4.4 Welcome Package Task
**Trigger:** Stage changes to "Active Partnership" (won deal)  
**Task Details:**
- Title: "Send welcome package"
- Due: +3 days
- Priority: Medium
- Owner: Onboarding user or opportunity owner

**Implementation:** `WorkflowService::createWelcomePackageTask()`

---

## Validation Rules

### 5.1 Stage Validation (Proposal Required)
**Rule:** Cannot move to "Contract & Commitment" without `proposal_sent_date`  
**Error Message:** "Proposal Sent Date is required before moving to Contract & Commitment."  
**Implementation:** `Sponsorship::canMoveToStage()` + `SponsorshipObserver::updating()`

### 5.2 Loss Reason Requirement
**Rule:** When marking stage = "Closed Lost", `loss_reason` must be provided  
**Implementation:** Form validation in Livewire component

### 5.3 Contract Date Validation
**Rule:** When moving to "Active Partnership", `contract_signed_date` must be set  
**Implementation:** `Sponsorship::canMoveToStage()` + `SponsorshipObserver::updating()`

---

## Scheduled Jobs

### Job Schedule Overview
```
02:00 - UpdateDaysInStage (nightly computation)
08:00 - CheckStagnantOpportunities
08:00 - SendFollowUpReminders
08:00 - SendOverdueTasksDigest
09:00 - CheckProposalFollowUps
```

### Job Configuration
All jobs are:
- ✅ Queued (ShouldQueue)
- ✅ Retry-enabled (3 attempts)
- ✅ Timeout-protected (300-600 seconds)
- ✅ Logged (success/failure tracking)
- ✅ Paginated (to avoid memory issues)

### Running the Scheduler
Ensure your cron is configured:
```bash
* * * * * cd /path-to-bamecrm && php artisan schedule:run >> /dev/null 2>&1
```

### Manual Job Execution (for testing)
```bash
php artisan queue:work
php artisan schedule:test
```

---

## Setup & Configuration

### 1. Run Migrations
```bash
php artisan migrate
```

This will create:
- Workflow fields on `sponsorships` table
- `tasks` table
- `sponsorship_logs` table

### 2. Configure Queue
Update `.env`:
```env
QUEUE_CONNECTION=database
# or use redis for better performance
QUEUE_CONNECTION=redis
```

Run queue worker:
```bash
php artisan queue:work --tries=3
```

### 3. Configure Mail
Update `.env` with your mail settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bamecrm.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Set Up Cron
Add to crontab:
```bash
* * * * * cd /path-to-bamecrm && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Verify Observer Registration
The `SponsorshipObserver` is automatically registered in `AppServiceProvider::boot()`.

---

## Testing

### Test Automated Field Updates
```php
// Create a sponsorship
$sponsorship = Sponsorship::create([
    'company_name' => 'Test Company',
    'stage' => 'Prospect Identification',
    // ... other fields
]);

// Verify probability is set
assert($sponsorship->probability === 10);

// Change stage
$sponsorship->update(['stage' => 'Negotiation']);

// Verify probability updated
assert($sponsorship->probability === 70);

// Verify stage_entry_date updated
assert($sponsorship->stage_entry_date->isToday());
```

### Test Task Creation
```php
// Move to Initial Outreach
$sponsorship->update(['stage' => 'Initial Outreach']);

// Verify task created
$task = Task::where('sponsorship_id', $sponsorship->id)
    ->where('automation_type', 'initial_outreach')
    ->first();

assert($task !== null);
assert($task->title === 'Send introduction email');
assert($task->due_date->isTomorrow());
```

### Test Notifications
```php
use Illuminate\Support\Facades\Notification;

Notification::fake();

// Move to Negotiation
$sponsorship->update(['stage' => 'Negotiation']);

// Assert notification sent
Notification::assertSentTo(
    $sponsorship->user,
    StageProgressionNotification::class
);
```

### Test Scheduled Jobs
```bash
# Test stagnant opportunities
php artisan tinker
>>> dispatch(new \App\Jobs\CheckStagnantOpportunities);

# Test follow-up reminders
>>> dispatch(new \App\Jobs\SendFollowUpReminders);

# View scheduled jobs
php artisan schedule:list
```

### Test Validation Rules
```php
// Try to move to Contract & Commitment without proposal
$sponsorship->proposal_sent_date = null;
$sponsorship->stage = 'Contract & Commitment';

try {
    $sponsorship->save();
    assert(false, 'Should have thrown validation exception');
} catch (\Illuminate\Validation\ValidationException $e) {
    assert(true);
}
```

---

## Monitoring & Logging

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Check Job Status
```bash
php artisan queue:failed
php artisan queue:retry all
```

### View Sponsorship Logs
```php
$sponsorship = Sponsorship::find(1);
$logs = $sponsorship->logs()->latest()->get();

foreach ($logs as $log) {
    echo "{$log->event_type}: {$log->description}\n";
}
```

---

## Troubleshooting

### Jobs Not Running
1. Check cron is configured: `crontab -l`
2. Check queue worker is running: `ps aux | grep queue:work`
3. Check failed jobs: `php artisan queue:failed`

### Notifications Not Sending
1. Check mail configuration in `.env`
2. Test mail: `php artisan tinker` → `Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });`
3. Check notification table: `SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10;`

### Observer Not Firing
1. Verify registration in `AppServiceProvider::boot()`
2. Clear cache: `php artisan optimize:clear`
3. Check if using `update()` vs `save()` (both should work)

---

## Security Considerations

1. **Authorization:** Ensure users can only see their own notifications
2. **Rate Limiting:** Notifications are queued to prevent spam
3. **Data Privacy:** Sensitive deal information is only sent to authorized recipients
4. **Audit Trail:** All changes are logged in `sponsorship_logs`

---

## Performance Optimization

1. **Queue Jobs:** All notifications and heavy operations are queued
2. **Batch Processing:** Scheduled jobs process in chunks
3. **Indexes:** Database indexes on frequently queried fields
4. **Caching:** Consider caching frequently accessed data

---

## Future Enhancements

- [ ] WhatsApp integration for notifications
- [ ] Slack/Teams integration
- [ ] Custom workflow builder UI
- [ ] Advanced reporting on automation effectiveness
- [ ] AI-powered next-best-action recommendations
- [ ] Webhook endpoints for external integrations

---

## Support

For issues or questions, contact the development team or refer to:
- Laravel Documentation: https://laravel.com/docs
- Laravel Notifications: https://laravel.com/docs/notifications
- Laravel Task Scheduling: https://laravel.com/docs/scheduling
