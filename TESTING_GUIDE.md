# Testing Guide

## Overview

This guide covers running tests to verify BameCRM functionality before and after deployment.

## Test Suites

### 1. Smoke Tests (`tests/Feature/SmokeTest.php`)
Basic functionality tests to ensure critical features work:
- ✅ Health check endpoint
- ✅ Login/logout functionality
- ✅ All main pages load
- ✅ Authentication and authorization
- ✅ Database connectivity
- ✅ Rate limiting
- ✅ HTTPS redirect
- ✅ Password reset flow

### 2. API Tests (`tests/Feature/ApiTest.php`)
API endpoint validation:
- ✅ API authentication
- ✅ Lead ingestion
- ✅ Input validation
- ✅ Rate limiting

### 3. Backup Tests (`tests/Feature/BackupTest.php`)
Backup functionality:
- ✅ Backup creation
- ✅ Old backup cleanup

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Smoke tests only
php artisan test --testsuite=Feature --filter=SmokeTest

# API tests only
php artisan test --testsuite=Feature --filter=ApiTest

# Backup tests only
php artisan test --testsuite=Feature --filter=BackupTest
```

### Run Specific Test
```bash
php artisan test --filter=test_login_page_loads
```

### Run with Coverage (requires Xdebug)
```bash
php artisan test --coverage
```

### Run in Parallel (faster)
```bash
php artisan test --parallel
```

## Test Output

### Success
```
PASS  Tests\Feature\SmokeTest
✓ health check endpoint is accessible
✓ login page loads
✓ user can login
✓ dashboard is accessible when authenticated

Tests:    20 passed (20 assertions)
Duration: 2.34s
```

### Failure
```
FAIL  Tests\Feature\SmokeTest
✓ health check endpoint is accessible
✗ login page loads

Expected status code 200 but received 500.
Failed asserting that 500 is identical to 200.

Tests:    1 failed, 19 passed (20 assertions)
Duration: 2.34s
```

## Pre-Deployment Testing

Before deploying to production, run the full test suite:

```bash
# 1. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Run migrations (fresh database)
php artisan migrate:fresh --seed

# 3. Run all tests
php artisan test

# 4. Check for deprecations
php artisan test --display-deprecations
```

All tests should pass before deployment.

## Post-Deployment Testing

After deploying to production, run manual smoke tests:

### 1. Health Check
```bash
curl https://yourdomain.com/up
```
Expected: `200 OK`

### 2. Login Test
1. Visit `https://yourdomain.com`
2. Login with test credentials
3. Verify dashboard loads

### 3. API Test
```bash
curl -X POST https://yourdomain.com/api/leads/ingest \
  -H "X-API-KEY: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "company_name": "Test Company",
    "email": "test@example.com",
    "source": "API Test"
  }'
```
Expected: `201 Created`

### 4. Email Test
```bash
php artisan tinker
>>> \Illuminate\Support\Facades\Mail::raw('Test', function($msg) { $msg->to('your-email@example.com')->subject('Test'); });
```
Check your email inbox.

### 5. Backup Test
```bash
php artisan backup:database
```
Check `storage/app/backups/` for new backup file.

### 6. Queue Test
```bash
# Check queue worker is running
# Windows
nssm status BameCRMWorker

# Linux
sudo supervisorctl status bamecrm-worker:*
```

### 7. Logs Test
```bash
# Check logs are being written
tail -f storage/logs/laravel.log
```

## Continuous Testing

### During Development
```bash
# Watch mode - runs tests on file changes
php artisan test --watch
```

### Before Each Commit
```bash
# Quick smoke test
php artisan test --filter=SmokeTest
```

### Before Each Deployment
```bash
# Full test suite
php artisan test
```

## Writing New Tests

### Create Test File
```bash
php artisan make:test SponsorshipTest
```

### Basic Test Structure
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_sponsorship(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/sponsorships', [
                'company_name' => 'Test Company',
                'decision_maker_email' => 'test@example.com',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sponsorships', [
            'company_name' => 'Test Company',
        ]);
    }
}
```

## Test Database

Tests use a separate SQLite database in memory by default.

Configuration in `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

This ensures tests don't affect your development database.

## Common Assertions

```php
// HTTP Status
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated();
$response->assertRedirect();

// Content
$response->assertSee('Welcome');
$response->assertDontSee('Error');

// JSON
$response->assertJson(['success' => true]);
$response->assertJsonStructure(['data', 'message']);

// Database
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);
$this->assertDatabaseCount('users', 5);

// Authentication
$this->assertAuthenticated();
$this->assertGuest();
```

## Troubleshooting

### Tests Failing Locally?

1. **Clear caches**:
```bash
php artisan config:clear
php artisan cache:clear
```

2. **Check database**:
```bash
php artisan migrate:fresh
```

3. **Check environment**:
```bash
php artisan env
```

### Tests Slow?

1. **Use parallel testing**:
```bash
php artisan test --parallel
```

2. **Run specific tests**:
```bash
php artisan test --filter=SmokeTest
```

3. **Disable coverage**:
```bash
php artisan test --without-coverage
```

### Database Errors?

Make sure `phpunit.xml` has:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## CI/CD Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

## Test Coverage Goals

Aim for:
- **Critical paths**: 100% (login, payments, data loss)
- **Business logic**: 80%+
- **UI components**: 60%+
- **Overall**: 70%+

## Summary

### Before Deployment
```bash
php artisan test
```
All tests must pass.

### After Deployment
1. Check health endpoint
2. Test login
3. Test API
4. Verify emails
5. Check backups
6. Monitor logs

### Regular Testing
- Run tests before each commit
- Full suite before deployment
- Monitor production health checks

---

**Testing ensures reliability!** Run tests regularly to catch issues early.
