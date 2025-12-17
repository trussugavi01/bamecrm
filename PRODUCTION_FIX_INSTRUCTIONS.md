# Production Password Reset Fix Instructions

## Problem
The forgot password functionality is returning a 500 error on the production site (https://bamehcrm.online).

## Root Causes
1. Missing `password_reset_tokens` table in production database
2. Possible mail configuration issues
3. Missing error handling (now fixed)

## Solution Steps

### Step 1: Upload Updated Files
Upload these files to your production server:
- `app/Livewire/Auth/ForgotPassword.php` (updated with error handling)
- `database/migrations/2024_12_17_000001_ensure_password_reset_tokens_table.php` (new migration)
- `.env` (with production settings)

### Step 2: Run Migration on Production
SSH into your production server and run:

```bash
cd /path/to/your/laravel/app
php artisan migrate --force
```

This will create the `password_reset_tokens` table if it doesn't exist.

### Step 3: Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Step 4: Verify Database Table
Check that the table was created:
```bash
php artisan tinker
```
Then in tinker:
```php
Schema::hasTable('password_reset_tokens')
// Should return: true
```

### Step 5: Test Email Configuration
Test that emails can be sent:
```bash
php artisan tinker
```
Then:
```php
Mail::raw('Test email', function($message) {
    $message->to('your-email@example.com')->subject('Test');
});
```

Check if you receive the test email. If not, verify your `.env` mail settings:
- `MAIL_MAILER=smtp`
- `MAIL_HOST=smtp.zeptomail.com`
- `MAIL_PORT=587`
- `MAIL_USERNAME=emailapikey`
- `MAIL_PASSWORD=your-zeptomail-token`
- `MAIL_ENCRYPTION=tls`
- `MAIL_FROM_ADDRESS=noreply@bamehscawards.org`

### Step 6: Check Logs
If the issue persists, check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

The error handling we added will now log detailed error information.

### Step 7: Verify File Permissions
Ensure storage directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Quick Verification Checklist
- [ ] `.env` file has correct production settings (APP_ENV=production, APP_URL=https://bamehcrm.online)
- [ ] `password_reset_tokens` table exists in database
- [ ] Mail configuration is correct and tested
- [ ] All caches are cleared
- [ ] Storage directories have correct permissions
- [ ] Error logs show no database or mail errors

## Testing
1. Visit https://bamehcrm.online/forgot-password
2. Enter a valid email address
3. Submit the form
4. Check for success message
5. Check email inbox for reset link
6. Click reset link and verify it works

## Common Issues

### Issue: "Table 'password_reset_tokens' doesn't exist"
**Solution:** Run the migration (Step 2)

### Issue: "Connection refused" or mail errors
**Solution:** Verify ZeptoMail credentials and that your domain is verified in ZeptoMail

### Issue: Still getting 500 error
**Solution:** Check `storage/logs/laravel.log` for the actual error message

## Support
If issues persist after following these steps, check the logs and provide the error message for further assistance.
