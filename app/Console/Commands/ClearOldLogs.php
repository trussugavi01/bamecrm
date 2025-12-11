<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing old log files...');

        $logsPath = storage_path('logs');
        $keepDays = (int) $this->option('days');
        $cutoffDate = now()->subDays($keepDays);
        
        $deletedCount = 0;
        $deletedSize = 0;

        // Get all log files
        $files = File::glob($logsPath . '/*.log');
        
        foreach ($files as $file) {
            // Skip the current day's log
            if (basename($file) === 'laravel.log') {
                continue;
            }

            $fileTime = File::lastModified($file);
            
            if ($fileTime < $cutoffDate->timestamp) {
                $size = File::size($file);
                File::delete($file);
                $deletedCount++;
                $deletedSize += $size;
                
                $this->line('  Deleted: ' . basename($file) . ' (' . $this->formatBytes($size) . ')');
            }
        }

        if ($deletedCount > 0) {
            $this->info("✓ Deleted {$deletedCount} log file(s) (" . $this->formatBytes($deletedSize) . " freed)");
        } else {
            $this->info('No old log files to delete');
        }

        // Show remaining logs
        $remainingFiles = File::glob($logsPath . '/*.log');
        $totalSize = array_sum(array_map(fn($f) => File::size($f), $remainingFiles));
        
        $this->line("\nRemaining logs: " . count($remainingFiles) . " (" . $this->formatBytes($totalSize) . ")");

        return 0;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
