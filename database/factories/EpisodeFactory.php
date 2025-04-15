<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpisodeFactory extends Factory
{
    protected $model = Episode::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'audio_url' => $this->faker->url(),
            'duration' => $this->faker->numberBetween(1200, 3600), // 20-60 minutes
            'podcast_id' => Podcast::factory(),
        ];
    }
} 