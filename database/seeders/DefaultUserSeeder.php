<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user (Ruth)
        User::firstOrCreate(
            ['email' => 'admin@bamecrm.com'],
            [
                'name' => 'Ruth Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create sample consultant user
        User::firstOrCreate(
            ['email' => 'consultant@bamecrm.com'],
            [
                'name' => 'Hap Consultant',
                'password' => Hash::make('password'),
                'role' => 'consultant',
            ]
        );

        // Create sample executive user
        User::firstOrCreate(
            ['email' => 'executive@bamecrm.com'],
            [
                'name' => 'Leadership Executive',
                'password' => Hash::make('password'),
                'role' => 'executive',
            ]
        );

        // Create sample approver user
        User::firstOrCreate(
            ['email' => 'approver@bamecrm.com'],
            [
                'name' => 'Content Approver',
                'password' => Hash::make('password'),
                'role' => 'approver',
            ]
        );
    }
}
