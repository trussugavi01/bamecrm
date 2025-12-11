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

        // Tasks for follow-ups
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('sponsorship_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('workflow_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

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
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('workflows');
    }
};
