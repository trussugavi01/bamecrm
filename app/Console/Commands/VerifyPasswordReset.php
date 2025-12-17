<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VerifyPasswordReset extends Command
{
    protected $signature = 'verify:password-reset';
    protected $description = 'Verify password reset functionality is properly configured';

    public function handle()
    {
        $this->info('Verifying Password Reset Configuration...');
        $this->newLine();

        $allGood = true;

        // Check 1: Database table exists
        $this->info('1. Checking password_reset_tokens table...');
        if (Schema::hasTable('password_reset_tokens')) {
            $this->line('   ✓ Table exists');
            
            // Check table structure
            $columns = Schema::getColumnListing('password_reset_tokens');
            $requiredColumns = ['email', 'token', 'created_at'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (empty($missingColumns)) {
                $this->line('   ✓ Table structure is correct');
            } else {
                $this->error('   ✗ Missing columns: ' . implode(', ', $missingColumns));
                $allGood = false;
            }
        } else {
            $this->error('   ✗ Table does not exist');
            $this->warn('   Run: php artisan migrate --force');
            $allGood = false;
        }

        $this->newLine();

        // Check 2: Mail configuration
        $this->info('2. Checking mail configuration...');
        $mailDriver = config('mail.default');
        $mailHost = config('mail.mailers.smtp.host');
        $mailFrom = config('mail.from.address');
        
        $this->line("   Mail Driver: {$mailDriver}");
        $this->line("   Mail Host: {$mailHost}");
        $this->line("   Mail From: {$mailFrom}");
        
        if ($mailDriver === 'log') {
            $this->warn('   ⚠ Mail driver is set to "log" - emails will not be sent');
        } elseif ($mailDriver === 'smtp' && $mailHost) {
            $this->line('   ✓ SMTP configuration looks good');
        }

        $this->newLine();

        // Check 3: Storage permissions
        $this->info('3. Checking storage permissions...');
        $storagePath = storage_path('logs');
        if (is_writable($storagePath)) {
            $this->line('   ✓ Storage directory is writable');
        } else {
            $this->error('   ✗ Storage directory is not writable');
            $this->warn('   Run: chmod -R 775 storage');
            $allGood = false;
        }

        $this->newLine();

        // Check 4: Environment settings
        $this->info('4. Checking environment settings...');
        $appEnv = config('app.env');
        $appUrl = config('app.url');
        $appDebug = config('app.debug');
        
        $this->line("   Environment: {$appEnv}");
        $this->line("   App URL: {$appUrl}");
        $this->line("   Debug Mode: " . ($appDebug ? 'ON' : 'OFF'));
        
        if ($appEnv === 'production' && $appDebug) {
            $this->warn('   ⚠ Debug mode is ON in production - should be OFF');
        }

        $this->newLine();

        // Summary
        if ($allGood) {
            $this->info('✓ All checks passed! Password reset should work correctly.');
        } else {
            $this->error('✗ Some issues found. Please fix them and run this command again.');
        }

        return $allGood ? 0 : 1;
    }
}
