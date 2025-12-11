<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pipeline;
use App\Models\PipelineStage;

class DefaultPipelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Check if user already has a default pipeline
            $existingPipeline = Pipeline::where('user_id', $user->id)
                ->where('is_default', true)
                ->first();

            if (!$existingPipeline) {
                // Create default pipeline for user
                $pipeline = Pipeline::create([
                    'user_id' => $user->id,
                    'name' => 'Standard Sponsorship Pipeline',
                    'description' => 'Default pipeline for sponsorship deals',
                    'is_default' => true,
                    'is_active' => true,
                ]);

                // Create default stages
                $stages = Pipeline::defaultStages();
                foreach ($stages as $stageData) {
                    PipelineStage::create([
                        'pipeline_id' => $pipeline->id,
                        'name' => $stageData['name'],
                        'probability' => $stageData['probability'],
                        'order' => $stageData['order'],
                        'color' => $stageData['color'],
                    ]);
                }

                $this->command->info("Created default pipeline for user: {$user->name}");
            }
        }

        $this->command->info('Default pipeline seeding completed!');
    }
}
