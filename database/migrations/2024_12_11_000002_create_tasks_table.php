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
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sponsorship_id')->constrained()->onDelete('cascade');
                $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->date('due_date')->nullable();
                $table->datetime('completed_at')->nullable();
                $table->boolean('is_automated')->default(false);
                $table->string('automation_type')->nullable(); // e.g., 'initial_outreach', 'proposal_followup'
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('due_date');
                $table->index('status');
                $table->index(['owner_id', 'status', 'due_date']);
                $table->index(['sponsorship_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
