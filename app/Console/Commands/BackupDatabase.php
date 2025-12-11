<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--keep=7 : Number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the SQLite database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        try {
            // Get database path
            $dbPath = database_path('database.sqlite');
            
            if (!File::exists($dbPath)) {
                $this->error('Database file not found at: ' . $dbPath);
                return 1;
            }

            // Create backup directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Generate backup filename with timestamp
            $timestamp = now()->format('Y-m-d_His');
            $backupFilename = "database_backup_{$timestamp}.sqlite";
            $backupPath = $backupDir . '/' . $backupFilename;

            // Copy database file
            File::copy($dbPath, $backupPath);

            // Get file size
            $fileSize = $this->formatBytes(File::size($backupPath));

            $this->info("✓ Backup created successfully!");
            $this->line("  Location: {$backupPath}");
            $this->line("  Size: {$fileSize}");

            // Clean up old backups
            $keepDays = (int) $this->option('keep');
            $this->cleanOldBackups($backupDir, $keepDays);

            return 0;

        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clean up old backup files
     */
    protected function cleanOldBackups(string $backupDir, int $keepDays): void
    {
        $this->info("\nCleaning up old backups (keeping last {$keepDays} days)...");

        $files = File::glob($backupDir . '/database_backup_*.sqlite');
        $cutoffDate = now()->subDays($keepDays);
        $deletedCount = 0;

        foreach ($files as $file) {
            $fileTime = File::lastModified($file);
            
            if ($fileTime < $cutoffDate->timestamp) {
                File::delete($file);
                $deletedCount++;
                $this->line("  Deleted: " . basename($file));
            }
        }

        if ($deletedCount > 0) {
            $this->info("✓ Deleted {$deletedCount} old backup(s)");
        } else {
            $this->line("  No old backups to delete");
        }

        // Show remaining backups
        $remainingFiles = File::glob($backupDir . '/database_backup_*.sqlite');
        $this->line("\nTotal backups: " . count($remainingFiles));
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
