<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'slug' => Str::slug($name)
        ];
    }
} 