<?php

namespace App\Jobs;

use App\Models\Sponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateDaysInStage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('UpdateDaysInStage job started');

        try {
            // 2.3 Days in Current Stage
            // Update days_in_stage for all active opportunities
            $updated = DB::update("
                UPDATE sponsorships
                SET days_in_stage = CASE
                    WHEN stage_entry_date IS NOT NULL
                    THEN CAST((julianday('now') - julianday(stage_entry_date)) AS INTEGER)
                    ELSE 0
                END
                WHERE deleted_at IS NULL
                AND stage NOT IN ('Active Partnership', 'Closed Lost')
            ");

            Log::info('UpdateDaysInStage job completed', [
                'updated_count' => $updated,
            ]);

        } catch (\Exception $e) {
            Log::error('UpdateDaysInStage job failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
