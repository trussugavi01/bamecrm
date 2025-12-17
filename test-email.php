<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing ZeptoMail configuration...\n\n";

try {
    Mail::raw('This is a test email from B.A.M.E CRM to verify ZeptoMail configuration is working correctly.', function($message) {
        $message->to('test@example.com')
                ->subject('Test Email - B.A.M.E CRM');
    });
    
    echo "✓ Email sent successfully!\n";
    echo "Check your email inbox (test@example.com) to confirm delivery.\n\n";
    echo "Note: Make sure to:\n";
    echo "1. Replace 'your-zeptomail-send-token-here' in .env with your actual ZeptoMail token\n";
    echo "2. Replace 'noreply@yourdomain.com' with your verified domain email\n";
    echo "3. Replace 'test@example.com' above with your actual email address\n";
    
} catch (\Exception $e) {
    echo "✗ Email failed to send!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Common issues:\n";
    echo "- Invalid ZeptoMail token in MAIL_PASSWORD\n";
    echo "- Unverified sender domain\n";
    echo "- Incorrect SMTP settings\n";
}
