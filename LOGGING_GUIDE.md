# Production Logging Guide

## Overview

Proper logging configuration is essential for monitoring and debugging production applications.

## Log Levels

Laravel uses standard PSR-3 log levels (from least to most severe):

1. **DEBUG** - Detailed debug information
2. **INFO** - Interesting events (user login, SQL logs)
3. **NOTICE** - Normal but significant events
4. **WARNING** - Exceptional occurrences that are not errors
5. **ERROR** - Runtime errors that don't require immediate action
6. **CRITICAL** - Critical conditions
7. **ALERT** - Action must be taken immediately
8. **EMERGENCY** - System is unusable

## Environment Configuration

### Development (Local)
```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug
```
- Logs everything to a single file
- Includes debug information
- Good for development

### Production
```env
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAILY_DAYS=14
```
- Creates daily log files (laravel-2025-12-07.log)
- Only logs errors and above
- Keeps 14 days of logs
- Automatic rotation

## Log Channels

### Stack (Default)
Combines multiple channels:
```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['single', 'slack'],
]
```

### Single
All logs in one file:
```php
'single' => [
    'driver' => 'single',
    'path' => storage_path('logs/laravel.log'),
]
```

### Daily (Recommended for Production)
Separate file per day:
```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'days' => 14,
]
```

### Slack (Optional)
Send critical errors to Slack:
```env
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/xxx
```

## Custom Logging

### In Controllers/Services
```php
use Illuminate\Support\Facades\Log;

// Different levels
Log::debug('Debug message', ['data' => $data]);
Log::info('User logged in', ['user_id' => $user->id]);
Log::warning('Disk space low', ['available' => $space]);
Log::error('Payment failed', ['order_id' => $order->id]);
Log::critical('Database connection lost');

// With context
Log::error('Failed to process order', [
    'order_id' => $order->id,
    'user_id' => $user->id,
    'error' => $exception->getMessage(),
]);
```

### In Blade Templates
```php
@php
    Log::info('View rendered', ['view' => 'dashboard']);
@endphp
```

### In Models
```php
protected static function booted()
{
    static::created(function ($model) {
        Log::info('Model created', [
            'model' => get_class($model),
            'id' => $model->id,
        ]);
    });
}
```

## Log Management

### View Logs
```bash
# View latest logs
tail -f storage/logs/laravel.log

# View last 100 lines
tail -n 100 storage/logs/laravel.log

# Search for errors
grep "ERROR" storage/logs/laravel.log

# View specific date
cat storage/logs/laravel-2025-12-07.log
```

### Clear Logs
```bash
# Clear old logs (keeps last 30 days)
php artisan logs:clear

# Keep last 60 days
php artisan logs:clear --days=60

# Manual clear
rm storage/logs/*.log
```

### Log Rotation
Automatic with daily driver:
- Creates new file each day
- Deletes files older than `LOG_DAILY_DAYS`
- Runs automatically

## Production Best Practices

### 1. Use Daily Logs
```env
LOG_CHANNEL=daily
LOG_DAILY_DAYS=14
```

### 2. Set Appropriate Level
```env
# Production - only errors
LOG_LEVEL=error

# Staging - warnings and errors
LOG_LEVEL=warning

# Development - everything
LOG_LEVEL=debug
```

### 3. Monitor Disk Space
```bash
# Check log directory size
du -sh storage/logs/

# Find large log files
find storage/logs/ -type f -size +10M
```

### 4. Set Up Alerts
Use Slack channel for critical errors:
```env
LOG_SLACK_WEBHOOK_URL=your-webhook-url
```

### 5. Regular Cleanup
Scheduled weekly cleanup:
```php
Schedule::command('logs:clear --days=30')->weekly();
```

## Log Analysis

### Common Patterns

#### Find Failed Logins
```bash
grep "Failed login" storage/logs/laravel.log
```

#### Find Database Errors
```bash
grep "QueryException" storage/logs/laravel.log
```

#### Count Errors by Type
```bash
grep "ERROR" storage/logs/laravel.log | cut -d' ' -f5 | sort | uniq -c
```

#### Find Slow Queries
```bash
grep "slow query" storage/logs/laravel.log
```

## Log Formats

### Standard Format
```
[2025-12-07 19:45:23] production.ERROR: Payment failed {"order_id":123,"user_id":45}
```

### With Stack Trace
```
[2025-12-07 19:45:23] production.ERROR: Call to undefined method
Stack trace:
#0 /app/Http/Controllers/OrderController.php(45): processPayment()
#1 /vendor/laravel/framework/...
```

## Advanced Configuration

### Custom Log Channel
In `config/logging.php`:
```php
'channels' => [
    'custom' => [
        'driver' => 'daily',
        'path' => storage_path('logs/custom.log'),
        'level' => 'info',
        'days' => 7,
    ],
],
```

Use it:
```php
Log::channel('custom')->info('Custom log message');
```

### Multiple Channels
```php
Log::stack(['single', 'slack'])->critical('Critical error');
```

### Conditional Logging
```php
if (app()->environment('production')) {
    Log::error('Production error', $context);
}
```

## Performance Considerations

### 1. Avoid Excessive Logging
```php
// Bad - logs every iteration
foreach ($items as $item) {
    Log::debug('Processing item', ['id' => $item->id]);
}

// Good - log summary
Log::info('Processed items', ['count' => count($items)]);
```

### 2. Use Appropriate Levels
```php
// Bad - debug in production
Log::debug('User clicked button');

// Good - only important events
Log::info('Order completed', ['order_id' => $order->id]);
```

### 3. Lazy Evaluation
```php
// Bad - always executes
Log::debug('Data: ' . json_encode($largeArray));

// Good - only if debug enabled
Log::debug('Data', ['data' => $largeArray]);
```

## Troubleshooting

### Logs Not Writing?

1. **Check permissions**:
```bash
chmod -R 775 storage/logs
chown -R www-data:www-data storage/logs
```

2. **Check disk space**:
```bash
df -h
```

3. **Check log level**:
```env
LOG_LEVEL=debug  # Temporarily for testing
```

### Logs Too Large?

1. **Reduce log level**:
```env
LOG_LEVEL=error
```

2. **Reduce retention**:
```env
LOG_DAILY_DAYS=7
```

3. **Clear old logs**:
```bash
php artisan logs:clear --days=7
```

## Security

### Don't Log Sensitive Data
```php
// Bad
Log::info('User login', [
    'email' => $email,
    'password' => $password,  // Never log passwords!
]);

// Good
Log::info('User login', [
    'email' => $email,
    'ip' => request()->ip(),
]);
```

### Sanitize User Input
```php
Log::info('Search query', [
    'query' => Str::limit($request->input('q'), 100),
]);
```

## Monitoring Tools

### Built-in
- Laravel Telescope (development)
- Laravel Horizon (queues)

### Third-party
- Sentry (error tracking)
- Papertrail (log management)
- Loggly (log analysis)
- Datadog (full monitoring)

## Summary

### Development
- Use `single` channel
- Log level: `debug`
- Keep all logs

### Production
- Use `daily` channel
- Log level: `error`
- Rotate after 14 days
- Monitor disk space
- Set up alerts for critical errors

### Commands
```bash
# View logs
tail -f storage/logs/laravel.log

# Clear old logs
php artisan logs:clear

# Test logging
php artisan tinker
>>> Log::info('Test message');
```
