<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Users in Database ===\n\n";

$users = User::all(['id', 'name', 'email', 'role']);

if ($users->isEmpty()) {
    echo "No users found in database!\n";
} else {
    echo "Total users: " . $users->count() . "\n\n";
    
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: {$user->role}\n";
        echo "---\n";
    }
}

echo "\n=== Testing Login Credentials ===\n\n";
echo "Enter the email you're trying to login with: ";
$testEmail = trim(fgets(STDIN));

$user = User::where('email', $testEmail)->first();

if ($user) {
    echo "✓ User found in database!\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n\n";
    
    echo "Enter the password you're trying to use: ";
    $testPassword = trim(fgets(STDIN));
    
    if (\Illuminate\Support\Facades\Hash::check($testPassword, $user->password)) {
        echo "✓ Password is CORRECT!\n";
        echo "\nThe login should work. Possible issues:\n";
        echo "- Browser cache (try clearing cookies)\n";
        echo "- Session issue (try incognito/private mode)\n";
        echo "- Livewire cache (run: php artisan livewire:discover)\n";
    } else {
        echo "✗ Password is INCORRECT!\n";
        echo "\nThe password you entered doesn't match the one in the database.\n";
        echo "You may need to reset the password.\n";
    }
} else {
    echo "✗ User with email '{$testEmail}' NOT found in database!\n";
    echo "\nAvailable emails:\n";
    foreach ($users as $u) {
        echo "  - {$u->email}\n";
    }
}
