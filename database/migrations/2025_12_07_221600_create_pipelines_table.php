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
        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('probability')->default(0);
            $table->integer('order')->default(0);
            $table->string('color')->default('#6B7280');
            $table->timestamps();
        });

        // Add pipeline_id to sponsorships table
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->foreignId('pipeline_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->dropForeign(['pipeline_id']);
            $table->dropColumn('pipeline_id');
        });
        
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('pipelines');
    }
};
