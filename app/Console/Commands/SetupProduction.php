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

        // Create default pipelines
        $this->info('Creating default pipelines...');
        
        $pipelines = [
            [
                'name' => 'New Lead',
                'description' => 'Initial contact stage',
                'order' => 1,
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Qualification',
                'description' => 'Qualifying the lead',
                'order' => 2,
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Proposal',
                'description' => 'Proposal sent',
                'order' => 3,
                'color' => '#EC4899',
            ],
            [
                'name' => 'Negotiation',
                'description' => 'In negotiation',
                'order' => 4,
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Closed Won',
                'description' => 'Successfully closed',
                'order' => 5,
                'color' => '#10B981',
            ],
            [
                'name' => 'Closed Lost',
                'description' => 'Lost opportunity',
                'order' => 6,
                'color' => '#EF4444',
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
