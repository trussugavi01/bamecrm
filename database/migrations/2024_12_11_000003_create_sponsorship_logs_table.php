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
        Schema::create('sponsorship_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsorship_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type'); // stage_change, field_update, status_change, etc.
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamp('created_at');
            
            // Indexes
            $table->index('sponsorship_id');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorship_logs');
    }
};
