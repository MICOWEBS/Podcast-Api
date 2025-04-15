<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

class PodcastFactory extends Factory
{
    protected $model = Podcast::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'image' => $this->faker->imageUrl(800, 600, 'business'),
            'is_featured' => $this->faker->boolean(20),
            'category_id' => Category::factory(),
        ];
    }
} 