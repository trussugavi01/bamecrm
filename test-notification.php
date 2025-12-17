<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Sponsorship;
use App\Notifications\FollowUpReminderNotification;

echo "Testing Email Notification System...\n\n";

try {
    // Get first user
    $user = User::first();
    
    if (!$user) {
        echo "✗ No users found in database. Please create a user first.\n";
        exit(1);
    }
    
    echo "✓ Found user: {$user->name} ({$user->email})\n";
    
    // Get first sponsorship
    $sponsorship = Sponsorship::first();
    
    if (!$sponsorship) {
        echo "✗ No sponsorships found. Creating a test notification anyway...\n";
        
        // Send a simple test email instead
        \Illuminate\Support\Facades\Mail::raw('This is a test email from B.A.M.E CRM to verify the queue worker and ZeptoMail are working correctly.', function($message) use ($user) {
            $message->to($user->email)
                    ->subject('Test Email - Queue Worker & ZeptoMail');
        });
        
        echo "✓ Test email queued successfully!\n";
        echo "\nCheck your queue worker terminal - you should see it processing the job.\n";
        echo "Then check {$user->email} for the test email.\n";
        
    } else {
        echo "✓ Found sponsorship: {$sponsorship->company_name}\n\n";
        
        // Send notification (will be queued)
        $user->notify(new FollowUpReminderNotification($sponsorship));
        
        echo "✓ Notification queued successfully!\n\n";
        echo "What happens next:\n";
        echo "1. Check your queue worker terminal - you should see it processing the job\n";
        echo "2. Check {$user->email} for the notification email\n";
        echo "3. If you see errors in the queue worker, check your ZeptoMail credentials\n";
    }
    
    echo "\n✓ Test completed!\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
