<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test backup command creates backup file
     */
    public function test_backup_command_creates_backup_file(): void
    {
        $backupDir = storage_path('app/backups');
        
        // Clear existing backups
        if (File::exists($backupDir)) {
            File::cleanDirectory($backupDir);
        }

        // Run backup command
        $this->artisan('backup:database')
            ->assertExitCode(0);

        // Check backup file was created
        $files = File::glob($backupDir . '/database_backup_*.sqlite');
        $this->assertCount(1, $files);
    }

    /**
     * Test backup cleanup removes old files
     */
    public function test_backup_cleanup_removes_old_files(): void
    {
        $backupDir = storage_path('app/backups');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Create old backup file
        $oldFile = $backupDir . '/database_backup_2020-01-01_000000.sqlite';
        File::put($oldFile, 'old backup');
        touch($oldFile, strtotime('-10 days'));

        // Run backup with 7 day retention
        $this->artisan('backup:database --keep=7')
            ->assertExitCode(0);

        // Old file should be deleted
        $this->assertFalse(File::exists($oldFile));
    }
}
