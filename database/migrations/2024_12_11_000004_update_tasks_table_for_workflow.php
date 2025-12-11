<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('tasks', 'owner_id')) {
                // Add as nullable first, then update and make not null
                $table->unsignedBigInteger('owner_id')->nullable()->after('sponsorship_id');
            }
            if (!Schema::hasColumn('tasks', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('owner_id');
            }
            if (!Schema::hasColumn('tasks', 'is_automated')) {
                $table->boolean('is_automated')->default(false)->after('completed_at');
            }
            if (!Schema::hasColumn('tasks', 'automation_type')) {
                $table->string('automation_type')->nullable()->after('is_automated');
            }
        });

        // Update existing tasks to set owner_id from assigned_to or user_id
        DB::statement('UPDATE tasks SET owner_id = COALESCE(assigned_to, user_id, 1) WHERE owner_id IS NULL');
        
        // Now make owner_id not nullable and add foreign keys
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'owner_id')) {
                // Make owner_id not nullable
                DB::statement('ALTER TABLE tasks MODIFY owner_id BIGINT UNSIGNED NOT NULL');
                
                // Add foreign key constraints if they don't exist
                try {
                    $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
            
            if (Schema::hasColumn('tasks', 'created_by')) {
                try {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'automation_type')) {
                $table->dropColumn('automation_type');
            }
            if (Schema::hasColumn('tasks', 'is_automated')) {
                $table->dropColumn('is_automated');
            }
            if (Schema::hasColumn('tasks', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('tasks', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->dropColumn('owner_id');
            }
        });
    }
};
