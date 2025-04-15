<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Podcast;
use Illuminate\Database\Seeder;

class PodcastSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            // Create 3 regular podcasts per category
            for ($i = 1; $i <= 3; $i++) {
                Podcast::create([
                    'title' => "{$category->name} Podcast {$i}",
                    'description' => "A fascinating podcast about {$category->name} topics. Episode {$i} covers interesting aspects and latest developments in the field.",
                    'image' => "https://picsum.photos/800/600?random=" . uniqid(),
                    'is_featured' => false,
                    'category_id' => $category->id,
                ]);
            }

            // Create 1 featured podcast per category
            Podcast::create([
                'title' => "Featured {$category->name} Podcast",
                'description' => "The most popular podcast in the {$category->name} category. Join us for insightful discussions and expert interviews.",
                'image' => "https://picsum.photos/800/600?random=" . uniqid(),
                'is_featured' => true,
                'category_id' => $category->id,
            ]);
        }
    }
} 