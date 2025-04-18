<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Episode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'audio_url' => $this->faker->url() . '.mp3',
            'duration' => $this->faker->numberBetween(300, 3600), // 5-60 minutes
            'episode_number' => $this->faker->unique()->numberBetween(1, 100),
            'season_number' => $this->faker->numberBetween(1, 10),
            'publish_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'explicit' => $this->faker->boolean(),
            'keywords' => $this->faker->words(5),
            'guests' => [
                [
                    'name' => $this->faker->name(),
                    'role' => $this->faker->jobTitle(),
                ]
            ],
            'show_notes' => $this->faker->paragraph(),
            'transcript' => $this->faker->paragraphs(3, true),
            'podcast_id' => Podcast::factory(),
        ];
    }
} 