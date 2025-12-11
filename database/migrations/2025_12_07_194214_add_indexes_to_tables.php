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
        // Sponsorships table indexes
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->index('stage', 'idx_sponsorships_stage');
            $table->index('user_id', 'idx_sponsorships_user_id');
            $table->index('decision_maker_email', 'idx_sponsorships_email');
            $table->index('priority', 'idx_sponsorships_priority');
            $table->index('tier', 'idx_sponsorships_tier');
            $table->index('last_activity_at', 'idx_sponsorships_last_activity');
            $table->index('created_at', 'idx_sponsorships_created_at');
            $table->index(['stage', 'user_id'], 'idx_sponsorships_stage_user');
        });

        // Activities table indexes
        Schema::table('activities', function (Blueprint $table) {
            $table->index('sponsorship_id', 'idx_activities_sponsorship_id');
            $table->index('user_id', 'idx_activities_user_id');
            $table->index('type', 'idx_activities_type');
            $table->index('created_at', 'idx_activities_created_at');
            $table->index(['sponsorship_id', 'created_at'], 'idx_activities_sponsorship_date');
        });

        // Lead forms table indexes
        Schema::table('lead_forms', function (Blueprint $table) {
            $table->index('user_id', 'idx_lead_forms_user_id');
            $table->index('uuid', 'idx_lead_forms_uuid');
            $table->index('is_active', 'idx_lead_forms_active');
        });

        // Workflows table indexes
        Schema::table('workflows', function (Blueprint $table) {
            $table->index('user_id', 'idx_workflows_user_id');
            $table->index('trigger_type', 'idx_workflows_trigger_type');
            $table->index('is_active', 'idx_workflows_active');
            $table->index('last_executed_at', 'idx_workflows_last_executed');
        });

        // Workflow logs table indexes
        Schema::table('workflow_logs', function (Blueprint $table) {
            $table->index('workflow_id', 'idx_workflow_logs_workflow_id');
            $table->index('sponsorship_id', 'idx_workflow_logs_sponsorship_id');
            $table->index('status', 'idx_workflow_logs_status');
            $table->index('created_at', 'idx_workflow_logs_created_at');
        });

        // Tasks table indexes
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('user_id', 'idx_tasks_user_id');
            $table->index('assigned_to', 'idx_tasks_assigned_to');
            $table->index('sponsorship_id', 'idx_tasks_sponsorship_id');
            $table->index('status', 'idx_tasks_status');
            $table->index('priority', 'idx_tasks_priority');
            $table->index('due_date', 'idx_tasks_due_date');
            $table->index(['assigned_to', 'status'], 'idx_tasks_assigned_status');
        });

        // Notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id', 'idx_notifications_user_id');
            $table->index('is_read', 'idx_notifications_is_read');
            $table->index('type', 'idx_notifications_type');
            $table->index('created_at', 'idx_notifications_created_at');
            $table->index(['user_id', 'is_read'], 'idx_notifications_user_read');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
            $table->index('role', 'idx_users_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sponsorships table indexes
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->dropIndex('idx_sponsorships_stage');
            $table->dropIndex('idx_sponsorships_user_id');
            $table->dropIndex('idx_sponsorships_email');
            $table->dropIndex('idx_sponsorships_priority');
            $table->dropIndex('idx_sponsorships_tier');
            $table->dropIndex('idx_sponsorships_last_activity');
            $table->dropIndex('idx_sponsorships_created_at');
            $table->dropIndex('idx_sponsorships_stage_user');
        });

        // Activities table indexes
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('idx_activities_sponsorship_id');
            $table->dropIndex('idx_activities_user_id');
            $table->dropIndex('idx_activities_type');
            $table->dropIndex('idx_activities_created_at');
            $table->dropIndex('idx_activities_sponsorship_date');
        });

        // Lead forms table indexes
        Schema::table('lead_forms', function (Blueprint $table) {
            $table->dropIndex('idx_lead_forms_user_id');
            $table->dropIndex('idx_lead_forms_uuid');
            $table->dropIndex('idx_lead_forms_active');
        });

        // Workflows table indexes
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropIndex('idx_workflows_user_id');
            $table->dropIndex('idx_workflows_trigger_type');
            $table->dropIndex('idx_workflows_active');
            $table->dropIndex('idx_workflows_last_executed');
        });

        // Workflow logs table indexes
        Schema::table('workflow_logs', function (Blueprint $table) {
            $table->dropIndex('idx_workflow_logs_workflow_id');
            $table->dropIndex('idx_workflow_logs_sponsorship_id');
            $table->dropIndex('idx_workflow_logs_status');
            $table->dropIndex('idx_workflow_logs_created_at');
        });

        // Tasks table indexes
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_user_id');
            $table->dropIndex('idx_tasks_assigned_to');
            $table->dropIndex('idx_tasks_sponsorship_id');
            $table->dropIndex('idx_tasks_status');
            $table->dropIndex('idx_tasks_priority');
            $table->dropIndex('idx_tasks_due_date');
            $table->dropIndex('idx_tasks_assigned_status');
        });

        // Notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_id');
            $table->dropIndex('idx_notifications_is_read');
            $table->dropIndex('idx_notifications_type');
            $table->dropIndex('idx_notifications_created_at');
            $table->dropIndex('idx_notifications_user_read');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_role');
        });
    }
};
