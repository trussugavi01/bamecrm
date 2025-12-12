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
        // Workflows table
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type'); // stage_change, deal_stagnant, deal_created, deal_won, deal_lost, scheduled
            $table->json('trigger_config')->nullable(); // e.g., {"stage": "Proposal Development"}
            $table->json('actions'); // Array of actions to perform
            $table->boolean('is_active')->default(true);
            $table->integer('execution_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();
        });

        // Workflow execution logs
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->foreignId('sponsorship_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status'); // success, failed, skipped
            $table->text('message')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
        });

        // Tasks table is created by 2024_12_11_000002_create_tasks_table.php
        // Adding workflow_id column to existing tasks table
        if (!Schema::hasColumn('tasks', 'workflow_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('workflow_id')->nullable()->after('sponsorship_id')->constrained()->onDelete('set null');
            });
        }

        // In-app notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // info, warning, success, error
            $table->string('icon')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        
        // Remove workflow_id column from tasks table
        if (Schema::hasColumn('tasks', 'workflow_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['workflow_id']);
                $table->dropColumn('workflow_id');
            });
        }
        
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('workflows');
    }
};
