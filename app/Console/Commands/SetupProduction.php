<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetupProduction extends Command
{
    protected $signature = 'setup:production';
    protected $description = 'Setup production environment with default users and pipelines';

    public function handle()
    {
        $this->info('Setting up production environment...');

        // Check if users table exists
        if (!\Schema::hasTable('users')) {
            $this->error('Users table does not exist. Please run migrations first.');
            return 1;
        }

        // Create default users
        $this->info('Creating default users...');
        
        User::firstOrCreate(
            ['email' => 'admin@bamecrm.com'],
            [
                'name' => 'Ruth Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'consultant@bamecrm.com'],
            [
                'name' => 'Hap Consultant',
                'password' => Hash::make('password'),
                'role' => 'consultant',
            ]
        );

        User::firstOrCreate(
            ['email' => 'executive@bamecrm.com'],
            [
                'name' => 'Leadership Executive',
                'password' => Hash::make('password'),
                'role' => 'executive',
            ]
        );

        User::firstOrCreate(
            ['email' => 'approver@bamecrm.com'],
            [
                'name' => 'Content Approver',
                'password' => Hash::make('password'),
                'role' => 'approver',
            ]
        );

        $this->info('Default users created successfully!');

        // Get admin user for pipeline ownership
        $adminUser = User::where('email', 'admin@bamecrm.com')->first();

        // Create default pipelines
        $this->info('Creating default pipelines...');
        
        $pipelines = [
            [
                'name' => 'New Lead',
                'description' => 'Initial contact stage',
                'user_id' => $adminUser->id,
            ],
            [
                'name' => 'Qualification',
                'description' => 'Qualifying the lead',
                'user_id' => $adminUser->id,
            ],
            [
                'name' => 'Proposal',
                'description' => 'Proposal sent',
                'user_id' => $adminUser->id,
            ],
            [
                'name' => 'Negotiation',
                'description' => 'In negotiation',
                'user_id' => $adminUser->id,
            ],
            [
                'name' => 'Closed Won',
                'description' => 'Successfully closed',
                'user_id' => $adminUser->id,
            ],
            [
                'name' => 'Closed Lost',
                'description' => 'Lost opportunity',
                'user_id' => $adminUser->id,
            ],
        ];

        foreach ($pipelines as $pipeline) {
            \App\Models\Pipeline::firstOrCreate(
                ['name' => $pipeline['name']],
                $pipeline
            );
        }

        $this->info('Default pipelines created successfully!');
        $this->info('Production setup completed!');

        return 0;
    }
}
