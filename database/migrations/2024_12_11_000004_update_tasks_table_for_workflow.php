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
        // This migration is no longer needed as the create_tasks_table migration
        // already includes all these columns (owner_id, created_by, is_automated, automation_type)
        // Keeping this migration empty to maintain migration history
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
