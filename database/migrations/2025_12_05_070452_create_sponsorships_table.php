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
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name')->index();
            $table->string('decision_maker_name')->nullable();
            $table->string('decision_maker_email')->nullable()->index();
            $table->enum('tier', ['Platinum', 'Gold', 'Silver', 'Bronze', 'In-Kind']);
            $table->decimal('value', 10, 2)->default(0);
            $table->string('stage')->default('Prospect Identification');
            $table->integer('probability')->default(10);
            $table->enum('priority', ['Hot', 'Warm', 'Cold'])->default('Warm');
            $table->string('source')->default('Outreach');
            $table->date('proposal_sent_date')->nullable();
            $table->date('contract_signed_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->timestamp('last_activity_at')->useCurrent();
            $table->string('loss_reason')->nullable();
            $table->longText('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
