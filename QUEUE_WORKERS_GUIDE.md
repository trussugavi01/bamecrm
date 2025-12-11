# Queue Workers Setup Guide

## Overview

Queue workers process background jobs asynchronously, improving application performance by offloading time-consuming tasks.

## Why Use Queues?

- ⚡ **Faster response times** - Don't make users wait for slow operations
- 📧 **Email sending** - Send emails in background
- 🔄 **Workflow automation** - Process workflows asynchronously
- 📊 **Report generation** - Generate large reports without blocking
- 🔁 **Retry failed jobs** - Automatic retry with exponential backoff

## Configuration

### 1. Queue Connection

Already configured in `.env`:
```env
QUEUE_CONNECTION=database
```

This uses the database to store queued jobs (no Redis/Beanstalkd required).

### 2. Database Tables

Already created by migrations:
- `jobs` - Pending jobs
- `job_batches` - Batch job tracking
- `failed_jobs` - Failed job records

## Running Queue Workers

### Development (Windows)

#### Option 1: Artisan Command
```bash
php artisan queue:work
```

Press `Ctrl+C` to stop.

#### Option 2: With Auto-Reload
```bash
php artisan queue:listen
```

Automatically reloads on code changes (slower but convenient for development).

### Production (Windows)

#### Using NSSM (Recommended)

1. **Download NSSM**: https://nssm.cc/download
2. **Extract to** `C:\nssm\`
3. **Install service**:

```cmd
cd C:\nssm\win64
nssm install BameCRMWorker "C:\xampp\php\php.exe" "C:\xampp\htdocs\bamecrm\artisan queue:work database --sleep=3 --tries=3 --max-time=3600"
```

4. **Configure service**:
```cmd
nssm set BameCRMWorker AppDirectory C:\xampp\htdocs\bamecrm
nssm set BameCRMWorker AppStdout C:\xampp\htdocs\bamecrm\storage\logs\worker.log
nssm set BameCRMWorker AppStderr C:\xampp\htdocs\bamecrm\storage\logs\worker-error.log
```

5. **Start service**:
```cmd
nssm start BameCRMWorker
```

6. **Check status**:
```cmd
nssm status BameCRMWorker
```

7. **Stop service**:
```cmd
nssm stop BameCRMWorker
```

8. **Remove service** (if needed):
```cmd
nssm remove BameCRMWorker confirm
```

#### Using Task Scheduler

1. Open **Task Scheduler**
2. Create **Basic Task**
3. Name: `BAME CRM Queue Worker`
4. Trigger: **At startup**
5. Action: **Start a program**
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\bamecrm\artisan queue:work database --sleep=3 --tries=3`
   - Start in: `C:\xampp\htdocs\bamecrm`
6. Check **Run whether user is logged on or not**
7. Check **Run with highest privileges**

### Production (Linux)

#### Using Supervisor (Recommended)

1. **Install Supervisor**:
```bash
sudo apt-get install supervisor
```

2. **Create config** `/etc/supervisor/conf.d/bamecrm-worker.conf`:
```ini
[program:bamecrm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bamecrm/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/bamecrm/storage/logs/worker.log
stopwaitsecs=3600
```

3. **Start Supervisor**:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bamecrm-worker:*
```

4. **Check status**:
```bash
sudo supervisorctl status
```

5. **Restart workers**:
```bash
sudo supervisorctl restart bamecrm-worker:*
```

## Queue Worker Options

```bash
php artisan queue:work [options]
```

### Important Options:

| Option | Description | Example |
|--------|-------------|---------|
| `--sleep=3` | Seconds to sleep when no jobs | `--sleep=3` |
| `--tries=3` | Max attempts before failing | `--tries=3` |
| `--max-time=3600` | Max seconds to run (1 hour) | `--max-time=3600` |
| `--timeout=60` | Max seconds per job | `--timeout=60` |
| `--queue=default` | Specific queue to process | `--queue=emails` |
| `--daemon` | Run as daemon (production) | `--daemon` |

### Recommended Production Command:
```bash
php artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=60
```

## Dispatching Jobs

### Create a Job

```bash
php artisan make:job SendWelcomeEmail
```

### Job Example

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public $user
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
    }

    public function failed(\Throwable $exception): void
    {
        // Handle job failure
        Log::error('Welcome email failed', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

### Dispatch Job

```php
use App\Jobs\SendWelcomeEmail;

// Dispatch immediately
SendWelcomeEmail::dispatch($user);

// Dispatch with delay
SendWelcomeEmail::dispatch($user)->delay(now()->addMinutes(10));

// Dispatch to specific queue
SendWelcomeEmail::dispatch($user)->onQueue('emails');

// Dispatch after response sent
SendWelcomeEmail::dispatchAfterResponse($user);
```

## Current Queued Jobs

### Workflows
Already configured to run in queue:
```php
// In WorkflowService.php
dispatch(function () use ($workflow, $sponsorship) {
    // Execute workflow actions
})->onQueue('workflows');
```

### Email Notifications
Password reset emails automatically queued when using queue driver.

## Monitoring Queues

### Check Queue Status

```bash
# View pending jobs
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry [job-id]

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Database Queries

```sql
-- Pending jobs
SELECT * FROM jobs;

-- Failed jobs
SELECT * FROM failed_jobs ORDER BY failed_at DESC;

-- Job batches
SELECT * FROM job_batches;
```

## Troubleshooting

### Jobs Not Processing?

1. **Check worker is running**:
```bash
# Windows (NSSM)
nssm status BameCRMWorker

# Linux (Supervisor)
sudo supervisorctl status
```

2. **Check logs**:
```bash
tail -f storage/logs/worker.log
tail -f storage/logs/laravel.log
```

3. **Check database**:
```sql
SELECT COUNT(*) FROM jobs;
```

4. **Restart worker**:
```bash
php artisan queue:restart
```

### Jobs Failing?

1. **View failed jobs**:
```bash
php artisan queue:failed
```

2. **Check error details**:
```sql
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 1;
```

3. **Retry failed job**:
```bash
php artisan queue:retry [job-id]
```

### Worker Stops Unexpectedly?

1. **Check memory limit** in `php.ini`:
```ini
memory_limit = 512M
```

2. **Use max-time option**:
```bash
php artisan queue:work --max-time=3600
```

3. **Monitor with Supervisor** (auto-restart)

## Best Practices

### 1. Always Use Queues For:
- ✅ Sending emails
- ✅ Processing uploads
- ✅ Generating reports
- ✅ API calls to external services
- ✅ Image processing
- ✅ Workflow automation

### 2. Set Appropriate Timeouts
```php
public $timeout = 120; // 2 minutes
public $tries = 3;     // 3 attempts
```

### 3. Handle Failures
```php
public function failed(\Throwable $exception): void
{
    // Notify admin, log error, etc.
}
```

### 4. Use Job Batching
```php
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

Bus::batch([
    new ProcessOrder($order1),
    new ProcessOrder($order2),
    new ProcessOrder($order3),
])->dispatch();
```

### 5. Monitor Queue Size
```php
use Illuminate\Support\Facades\Queue;

$size = Queue::size('default');
if ($size > 1000) {
    // Alert admin
}
```

## Performance Optimization

### Multiple Workers
Run multiple workers for better throughput:

**Windows (NSSM)**:
```cmd
nssm install BameCRMWorker1 ...
nssm install BameCRMWorker2 ...
```

**Linux (Supervisor)**:
```ini
numprocs=4  # Run 4 workers
```

### Priority Queues
```php
// High priority
SendUrgentEmail::dispatch($user)->onQueue('high');

// Normal priority
SendNewsletter::dispatch($user)->onQueue('default');

// Low priority
GenerateReport::dispatch()->onQueue('low');
```

Process in order:
```bash
php artisan queue:work --queue=high,default,low
```

## Scheduled Queue Maintenance

Already configured in `routes/console.php`:
```php
Schedule::command('queue:prune-batches')->daily();
Schedule::command('queue:prune-failed --hours=168')->daily();
```

This automatically cleans up old job records.

## Testing

### Test Job Locally
```bash
php artisan tinker
>>> dispatch(new \App\Jobs\TestJob());
```

### Sync Queue (Testing)
In `.env` for testing:
```env
QUEUE_CONNECTION=sync
```

Jobs run immediately (synchronously) for easier testing.

## Summary

### Development
```bash
php artisan queue:work
```

### Production (Windows)
```bash
# Install NSSM service
nssm install BameCRMWorker "C:\xampp\php\php.exe" "C:\xampp\htdocs\bamecrm\artisan queue:work database --sleep=3 --tries=3 --max-time=3600"
nssm start BameCRMWorker
```

### Production (Linux)
```bash
# Setup Supervisor
sudo supervisorctl start bamecrm-worker:*
```

### Monitor
```bash
# Check status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed
php artisan queue:retry all
```

---

**Queue workers are essential for production!** They ensure emails, workflows, and background tasks run smoothly without blocking user requests.
