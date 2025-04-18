<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('language', 10)->default('en');
            $table->json('tags')->nullable();
            $table->string('author');
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('explicit')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('is_featured');
            $table->index('language');
            $table->index('author');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
}; 