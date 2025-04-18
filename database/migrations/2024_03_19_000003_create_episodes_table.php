<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('audio_url');
            $table->integer('duration')->comment('Duration in seconds');
            $table->integer('episode_number');
            $table->integer('season_number')->nullable();
            $table->timestamp('publish_date')->nullable();
            $table->boolean('explicit')->default(false);
            $table->json('keywords')->nullable();
            $table->json('guests')->nullable();
            $table->text('show_notes')->nullable();
            $table->longText('transcript')->nullable();
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['podcast_id', 'created_at']);
            $table->unique(['podcast_id', 'episode_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
}; 