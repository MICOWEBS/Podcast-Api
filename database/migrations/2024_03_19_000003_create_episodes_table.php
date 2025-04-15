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
            $table->string('audio_url');
            $table->integer('duration')->comment('Duration in seconds');
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['podcast_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
}; 