<?php

/**
 * Production User Check & Creation Script
 * 
 * Run this on production server to:
 * 1. Check if users exist
 * 2. Create missing users
 * 3. Reset passwords if needed
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== BAME CRM Production User Check ===\n\n";

// Check current users
$users = User::all();

echo "Current users in database: " . $users->count() . "\n\n";

if ($users->count() > 0) {
    echo "Existing users:\n";
    foreach ($users as $user) {
        echo "  - {$user->email} ({$user->role})\n";
    }
    echo "\n";
}

// Default users that should exist
$defaultUsers = [
    [
        'name' => 'Admin User',
        'email' => 'admin@bamecrm.com',
        'password' => 'password',
        'role' => 'admin',
    ],
    [
        'name' => 'Manager User',
        'email' => 'manager@bamecrm.com',
        'password' => 'password',
        'role' => 'consultant',
    ],
];

echo "=== Checking/Creating Default Users ===\n\n";

foreach ($defaultUsers as $userData) {
    $user = User::where('email', $userData['email'])->first();
    
    if ($user) {
        echo "✓ User exists: {$userData['email']}\n";
        
        // Ask if password should be reset
        echo "  Reset password to 'password'? (y/n): ";
        $reset = trim(fgets(STDIN));
        
        if (strtolower($reset) === 'y') {
            $user->password = Hash::make($userData['password']);
            $user->save();
            echo "  ✓ Password reset to 'password'\n";
        }
    } else {
        echo "✗ User missing: {$userData['email']}\n";
        echo "  Creating user...\n";
        
        User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
        ]);
        
        echo "  ✓ User created with password 'password'\n";
    }
    echo "\n";
}

echo "=== Custom User Creation ===\n\n";
echo "Create a new user? (y/n): ";
$createNew = trim(fgets(STDIN));

if (strtolower($createNew) === 'y') {
    echo "Enter name: ";
    $name = trim(fgets(STDIN));
    
    echo "Enter email: ";
    $email = trim(fgets(STDIN));
    
    echo "Enter password: ";
    $password = trim(fgets(STDIN));
    
    echo "Enter role (admin/consultant/executive/approver): ";
    $role = trim(fgets(STDIN));
    
    try {
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);
        
        echo "\n✓ User created successfully!\n";
        echo "  Email: {$email}\n";
        echo "  Password: {$password}\n";
        echo "  Role: {$role}\n";
    } catch (\Exception $e) {
        echo "\n✗ Error creating user: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Total users: " . User::count() . "\n";
echo "\nYou can now login with any of the above credentials.\n";
