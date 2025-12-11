<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            // Add workflow tracking fields if they don't exist
            if (!Schema::hasColumn('sponsorships', 'stage_entry_date')) {
                $table->date('stage_entry_date')->nullable()->after('stage');
            }
            if (!Schema::hasColumn('sponsorships', 'days_in_stage')) {
                $table->integer('days_in_stage')->default(0)->after('stage_entry_date');
            }
            if (!Schema::hasColumn('sponsorships', 'last_activity_date')) {
                $table->date('last_activity_date')->nullable()->after('last_activity_at');
            }
            if (!Schema::hasColumn('sponsorships', 'currency')) {
                $table->string('currency', 3)->default('GBP')->after('value');
            }
            if (!Schema::hasColumn('sponsorships', 'proposal_followup_task_created')) {
                $table->boolean('proposal_followup_task_created')->default(false)->after('proposal_sent_date');
            }
            
            // Add indexes for performance
            $table->index('stage_entry_date');
            $table->index('last_activity_date');
            $table->index('next_follow_up_date');
            $table->index(['stage', 'last_activity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->dropIndex(['sponsorships_stage_entry_date_index']);
            $table->dropIndex(['sponsorships_last_activity_date_index']);
            $table->dropIndex(['sponsorships_next_follow_up_date_index']);
            $table->dropIndex(['sponsorships_stage_last_activity_date_index']);
            
            $table->dropColumn([
                'stage_entry_date',
                'days_in_stage',
                'last_activity_date',
                'currency',
                'proposal_followup_task_created',
            ]);
        });
    }
};
