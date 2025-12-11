# Workflow & Automation System - Quick Setup Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Run Migrations
```bash
cd c:\xampp\htdocs\bamecrm
php artisan migrate
```

This creates:
- ✅ Workflow fields on sponsorships table
- ✅ Tasks table
- ✅ Sponsorship logs table

### Step 2: Clear Cache
```bash
php artisan optimize:clear
php artisan config:clear
php artisan view:clear
```

### Step 3: Start Queue Worker (New Terminal)
```bash
php artisan queue:work --tries=3
```

Keep this running in a separate terminal window.

### Step 4: Test the System

#### Test 1: Automated Probability Update
```bash
php artisan tinker
```

```php
// Create a test sponsorship
$sponsorship = App\Models\Sponsorship::create([
    'user_id' => 1,
    'company_name' => 'Test Automation Co',
    'stage' => 'Prospect Identification',
    'tier' => 'Gold',
    'value' => 50000,
    'priority' => 'Hot',
    'source' => 'Web Form',
]);

// Check probability was auto-set
echo "Probability: " . $sponsorship->probability . "%\n"; // Should be 10

// Change stage
$sponsorship->update(['stage' => 'Negotiation']);

// Check probability updated
$sponsorship->refresh();
echo "New Probability: " . $sponsorship->probability . "%\n"; // Should be 70

// Check stage_entry_date was set
echo "Stage Entry Date: " . $sponsorship->stage_entry_date . "\n";
```

#### Test 2: Automated Task Creation
```php
// Move to Initial Outreach
$sponsorship->update(['stage' => 'Initial Outreach']);

// Check if task was created
$task = App\Models\Task::where('sponsorship_id', $sponsorship->id)
    ->where('automation_type', 'initial_outreach')
    ->first();

echo "Task Created: " . ($task ? 'YES' : 'NO') . "\n";
echo "Task Title: " . $task->title . "\n";
echo "Due Date: " . $task->due_date . "\n";
```

#### Test 3: Stage Progression Notification
```php
use Illuminate\Support\Facades\Notification;

// Fake notifications to capture them
Notification::fake();

// Move to Negotiation (triggers notification)
$sponsorship->update(['stage' => 'Negotiation']);

// Check notification was queued
Notification::assertSentTo(
    $sponsorship->user,
    App\Notifications\StageProgressionNotification::class
);

echo "Notification Test: PASSED\n";
```

#### Test 4: Activity Logging
```php
$activityService = app(App\Services\ActivityService::class);

// Log a call
$activityService->logCall($sponsorship->id, 'Discussed pricing options', 30);

// Log an email
$activityService->logEmail($sponsorship->id, 'Follow-up on proposal', 'sent');

// Check last_activity_date was updated
$sponsorship->refresh();
echo "Last Activity Date: " . $sponsorship->last_activity_date . "\n";

// Get recent activities
$activities = $activityService->getRecentActivities($sponsorship->id);
echo "Activities Count: " . $activities->count() . "\n";
```

### Step 5: Test Scheduled Jobs Manually

#### Test Stagnant Opportunities Checker
```bash
php artisan tinker
```

```php
// Create a stagnant opportunity
$stagnant = App\Models\Sponsorship::create([
    'user_id' => 1,
    'company_name' => 'Stagnant Deal Inc',
    'stage' => 'Qualification & Discovery',
    'tier' => 'Silver',
    'value' => 25000,
    'priority' => 'Warm',
    'source' => 'Referral',
    'last_activity_date' => now()->subDays(20), // 20 days ago
]);

// Run the job
dispatch(new App\Jobs\CheckStagnantOpportunities);

// Check your email or notifications table
```

#### Test Follow-up Reminders
```php
// Create opportunity with follow-up tomorrow
$followup = App\Models\Sponsorship::create([
    'user_id' => 1,
    'company_name' => 'Follow-up Test Co',
    'stage' => 'Proposal Development',
    'tier' => 'Gold',
    'value' => 75000,
    'priority' => 'Hot',
    'source' => 'Outreach',
    'next_follow_up_date' => now()->addDay(),
]);

// Run the job
dispatch(new App\Jobs\SendFollowUpReminders);
```

#### Test Overdue Tasks Digest
```php
// Create an overdue task
App\Models\Task::create([
    'sponsorship_id' => $sponsorship->id,
    'owner_id' => 1,
    'created_by' => 1,
    'title' => 'Overdue Test Task',
    'description' => 'This task is overdue',
    'due_date' => now()->subDays(5),
    'priority' => 'high',
    'status' => 'pending',
]);

// Run the job
dispatch(new App\Jobs\SendOverdueTasksDigest);
```

#### Test Days in Stage Update
```php
// Run the job
dispatch(new App\Jobs\UpdateDaysInStage);

// Check if days_in_stage was updated
$sponsorship->refresh();
echo "Days in Stage: " . $sponsorship->days_in_stage . "\n";
```

#### Test Proposal Follow-up
```php
// Create opportunity with old proposal
$proposal = App\Models\Sponsorship::create([
    'user_id' => 1,
    'company_name' => 'Proposal Test Co',
    'stage' => 'Proposal Development',
    'tier' => 'Platinum',
    'value' => 100000,
    'priority' => 'Hot',
    'source' => 'Event',
    'proposal_sent_date' => now()->subDays(5),
    'proposal_followup_task_created' => false,
]);

// Run the job
dispatch(new App\Jobs\CheckProposalFollowUps);

// Check if task was created
$task = App\Models\Task::where('sponsorship_id', $proposal->id)
    ->where('automation_type', 'proposal_followup')
    ->first();

echo "Follow-up Task Created: " . ($task ? 'YES' : 'NO') . "\n";
```

---

## 📋 Verification Checklist

After setup, verify these items:

### Database
- [ ] `sponsorships` table has new workflow fields
- [ ] `tasks` table exists
- [ ] `sponsorship_logs` table exists
- [ ] Indexes are created

### Observer
- [ ] `SponsorshipObserver` is registered in `AppServiceProvider`
- [ ] Probability updates automatically on stage change
- [ ] `stage_entry_date` updates on stage change
- [ ] `last_activity_date` updates on any change

### Scheduled Jobs
- [ ] Jobs are registered in `routes/console.php`
- [ ] Cron is configured (for production)
- [ ] Queue worker is running

### Notifications
- [ ] Mail configuration is correct in `.env`
- [ ] Notifications table exists
- [ ] Test email sends successfully

### Tasks
- [ ] Tasks are created on stage changes
- [ ] Task relationships work (owner, sponsorship)
- [ ] Automated tasks have correct flags

---

## 🔍 Monitoring Commands

### View Scheduled Jobs
```bash
php artisan schedule:list
```

### Test Schedule (without waiting)
```bash
php artisan schedule:test
```

### View Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
php artisan queue:retry all
```

### View Recent Logs
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50

# Or open in editor
code storage/logs/laravel.log
```

### Check Database
```bash
php artisan tinker
```

```php
// Count sponsorships
App\Models\Sponsorship::count();

// Count tasks
App\Models\Task::count();

// Count logs
App\Models\SponsorshipLog::count();

// View recent notifications
DB::table('notifications')->latest()->limit(5)->get();
```

---

## 🐛 Troubleshooting

### Issue: Observer not firing
**Solution:**
```bash
php artisan optimize:clear
php artisan config:clear
```

### Issue: Jobs not running
**Solution:**
1. Check queue worker is running: `ps aux | grep queue:work` (Linux) or Task Manager (Windows)
2. Restart queue worker: `Ctrl+C` then `php artisan queue:work --tries=3`
3. Check failed jobs: `php artisan queue:failed`

### Issue: Notifications not sending
**Solution:**
1. Check `.env` mail configuration
2. Test mail: 
```php
Mail::raw('Test', function($msg) { 
    $msg->to('your@email.com')->subject('Test'); 
});
```
3. Check `notifications` table for database notifications

### Issue: Migrations fail
**Solution:**
```bash
# Check if columns already exist
php artisan tinker
>>> Schema::hasColumn('sponsorships', 'stage_entry_date');

# If true, the migration already ran or column exists
# You may need to modify the migration to check before adding
```

---

## 📊 Production Deployment

### 1. Set Up Cron (Linux/Mac)
```bash
crontab -e
```

Add:
```
* * * * * cd /path/to/bamecrm && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Set Up Task Scheduler (Windows)
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: Daily, repeat every 1 minute
4. Action: Start a program
5. Program: `C:\xampp\php\php.exe`
6. Arguments: `artisan schedule:run`
7. Start in: `C:\xampp\htdocs\bamecrm`

### 3. Configure Supervisor (Linux - Recommended)
```bash
sudo nano /etc/supervisor/conf.d/bamecrm-worker.conf
```

```ini
[program:bamecrm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/bamecrm/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/bamecrm/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bamecrm-worker:*
```

### 4. Configure Queue (Production)
Update `.env`:
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Install Redis:
```bash
# Ubuntu/Debian
sudo apt-get install redis-server

# Start Redis
sudo systemctl start redis
sudo systemctl enable redis
```

---

## 🎯 Next Steps

1. ✅ Complete this setup guide
2. ✅ Test all workflows manually
3. ✅ Monitor logs for 24 hours
4. ✅ Adjust notification timing if needed
5. ✅ Train users on new automated features
6. ✅ Set up monitoring/alerting for failed jobs
7. ✅ Document any custom workflows added

---

## 📞 Support

If you encounter issues:
1. Check the main `WORKFLOW_AUTOMATION_GUIDE.md`
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check queue logs if using Supervisor
4. Verify database migrations completed
5. Test individual components in isolation

---

## ✨ Success Indicators

You'll know the system is working when:
- ✅ Probability updates automatically when changing stages
- ✅ Tasks are created automatically at appropriate stages
- ✅ Email notifications arrive for stagnant deals
- ✅ Follow-up reminders are sent
- ✅ Overdue task digests arrive daily
- ✅ Stage progression notifications are sent
- ✅ Won deal celebrations are sent
- ✅ Days in stage updates nightly
- ✅ Activity logging updates last_activity_date
- ✅ All changes are logged in sponsorship_logs

**Congratulations! Your workflow automation system is now live! 🎉**
