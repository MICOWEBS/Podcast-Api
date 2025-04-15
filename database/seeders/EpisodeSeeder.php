<?php

namespace Database\Seeders;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Database\Seeder;

class EpisodeSeeder extends Seeder
{
    public function run(): void
    {
        $podcasts = Podcast::all();

        foreach ($podcasts as $podcast) {
            // Create 5 episodes per podcast
            for ($i = 1; $i <= 5; $i++) {
                Episode::create([
                    'title' => "Episode {$i}: " . fake()->sentence(),
                    'audio_url' => "https://example.com/audio/episode-{$i}.mp3",
                    'duration' => fake()->numberBetween(1200, 3600), // 20-60 minutes
                    'podcast_id' => $podcast->id,
                ]);
            }
        }
    }
} 