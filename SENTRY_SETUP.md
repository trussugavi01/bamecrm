# Sentry Error Monitoring Setup Guide

Sentry provides real-time error tracking and monitoring for your production application.

## Why Use Sentry?

- 🔍 **Real-time error tracking** - Get notified immediately when errors occur
- 📊 **Performance monitoring** - Track slow queries and requests
- 🐛 **Debug with context** - See user actions, environment, and stack traces
- 📈 **Error trends** - Identify patterns and recurring issues
- 👥 **Team collaboration** - Assign and track error resolution
- 🆓 **Free tier** - 5,000 errors/month free

## Setup Instructions

### 1. Create Sentry Account

1. Go to [https://sentry.io/signup/](https://sentry.io/signup/)
2. Sign up for a free account
3. Create a new project
4. Select **Laravel** as the platform

### 2. Get Your DSN

After creating the project, Sentry will show you a DSN (Data Source Name) that looks like:

```
https://examplePublicKey@o0.ingest.sentry.io/0
```

Copy this DSN.

### 3. Configure Environment

Add to your `.env` file:

```env
SENTRY_LARAVEL_DSN=https://your-dsn-here@sentry.io/project-id
SENTRY_TRACES_SAMPLE_RATE=1.0
```

**For production**, you may want to sample traces:
```env
SENTRY_TRACES_SAMPLE_RATE=0.2  # Track 20% of transactions
```

### 4. Publish Configuration (Optional)

If you want to customize Sentry settings:

```bash
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

This creates `config/sentry.php` where you can configure:
- Release tracking
- Environment detection
- Breadcrumb settings
- Performance monitoring

### 5. Test Error Tracking

Test that Sentry is working:

```bash
php artisan sentry:test
```

Or trigger a test error in your code:

```php
throw new \Exception('Test Sentry error tracking');
```

Visit your Sentry dashboard to see the error appear.

## What Gets Tracked

### Automatically Tracked:
- ✅ PHP exceptions and errors
- ✅ Failed jobs
- ✅ Slow database queries
- ✅ HTTP request performance
- ✅ User context (when authenticated)
- ✅ Environment details
- ✅ Stack traces

### Example Error in Sentry:

```
Exception: Call to undefined method
File: app/Http/Controllers/SponsorshipController.php:45
User: admin@bamecrm.com (ID: 1)
Environment: production
URL: https://yourdomain.com/sponsorships
Browser: Chrome 120.0
OS: Windows 10
```

## Manual Error Tracking

You can manually capture errors or messages:

```php
use Sentry\Laravel\Facade as Sentry;

// Capture exception
try {
    // risky code
} catch (\Exception $e) {
    Sentry::captureException($e);
}

// Capture message
Sentry::captureMessage('Something went wrong', 'warning');

// Add context
Sentry::configureScope(function ($scope) {
    $scope->setUser([
        'id' => auth()->id(),
        'email' => auth()->user()->email,
    ]);
    $scope->setTag('feature', 'sponsorships');
});
```

## Performance Monitoring

Track custom transactions:

```php
$transaction = \Sentry\startTransaction(['name' => 'Generate Report']);

// Your code here
$report = $this->generateReport();

$transaction->finish();
```

## Best Practices

### 1. Set Release Versions

In `config/sentry.php`:
```php
'release' => env('SENTRY_RELEASE', 'bamecrm@' . config('app.version')),
```

### 2. Filter Sensitive Data

Sentry automatically filters:
- Passwords
- Credit card numbers
- API keys

Add custom filters in `config/sentry.php`:
```php
'send_default_pii' => false,
'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
    // Remove sensitive data
    return $event;
},
```

### 3. Set Up Alerts

In Sentry dashboard:
1. Go to **Alerts** → **Create Alert**
2. Set conditions (e.g., "Error count > 10 in 1 hour")
3. Choose notification method (Email, Slack, etc.)

### 4. Ignore Common Errors

In `config/sentry.php`:
```php
'ignore_exceptions' => [
    Illuminate\Auth\AuthenticationException::class,
    Illuminate\Validation\ValidationException::class,
],
```

## Sentry Dashboard Features

### Issues Tab
- View all errors grouped by type
- See frequency and affected users
- Mark as resolved or ignored

### Performance Tab
- Track slow endpoints
- Monitor database query performance
- Identify bottlenecks

### Releases Tab
- Track errors by deployment version
- See if new releases introduce bugs
- Monitor error trends over time

## Troubleshooting

### Errors Not Appearing?

1. **Check DSN is set**: `php artisan config:clear`
2. **Test connection**: `php artisan sentry:test`
3. **Check environment**: Sentry only tracks in production by default
4. **Verify package installed**: `composer show sentry/sentry-laravel`

### Too Many Errors?

1. **Adjust sample rate**: Lower `SENTRY_TRACES_SAMPLE_RATE`
2. **Ignore exceptions**: Add to `ignore_exceptions` in config
3. **Set up filters**: Use `before_send` callback

### Local Development

By default, Sentry is disabled in local environment. To enable:

```env
APP_ENV=local
SENTRY_LARAVEL_DSN=your-dsn  # Will still track
```

Or explicitly disable:
```php
// In config/sentry.php
'dsn' => env('APP_ENV') === 'production' ? env('SENTRY_LARAVEL_DSN') : null,
```

## Cost & Limits

### Free Tier (Developer)
- 5,000 errors/month
- 10,000 performance units/month
- 30-day data retention
- 1 project

### Team Tier ($26/month)
- 50,000 errors/month
- 100,000 performance units/month
- 90-day data retention
- Unlimited projects

For most small to medium applications, the free tier is sufficient.

## Alternative: Disable Sentry

If you don't want to use Sentry, simply leave `SENTRY_LARAVEL_DSN` empty in `.env`.

The application will work normally without error tracking.

## Support

- **Sentry Docs**: https://docs.sentry.io/platforms/php/guides/laravel/
- **Laravel Integration**: https://github.com/getsentry/sentry-laravel
- **Community**: https://forum.sentry.io/

---

**Note**: Sentry is optional but highly recommended for production. It helps you catch and fix errors before users report them.
