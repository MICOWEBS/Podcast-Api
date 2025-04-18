<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PodcastFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(3),
            'image' => $this->faker->imageUrl(640, 480, 'podcast', true),
            'language' => $this->faker->randomElement(['en', 'es', 'fr', 'de', 'it']),
            'tags' => json_encode($this->faker->words(5)),
            'author' => $this->faker->name,
            'website' => $this->faker->url,
            'social_links' => json_encode([
                'twitter' => $this->faker->url,
                'facebook' => $this->faker->url,
                'instagram' => $this->faker->url
            ]),
            'explicit' => $this->faker->boolean,
            'is_featured' => $this->faker->boolean,
            'category_id' => Category::factory(),
            'user_id' => User::factory()
        ];
    }
} 