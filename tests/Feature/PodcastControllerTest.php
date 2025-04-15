<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PodcastControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_podcasts(): void
    {
        // Create a category and some podcasts
        $category = Category::factory()->create();
        Podcast::factory()->count(3)->create(['category_id' => $category->id]);

        // Make the API request
        $response = $this->getJson('/api/podcasts');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'image',
                        'is_featured',
                        'category',
                        'episodes_count',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_can_get_featured_podcasts(): void
    {
        // Create featured and non-featured podcasts
        $category = Category::factory()->create();
        Podcast::factory()->create(['category_id' => $category->id, 'is_featured' => true]);
        Podcast::factory()->create(['category_id' => $category->id, 'is_featured' => false]);

        // Make the API request
        $response = $this->getJson('/api/podcasts?featured=true');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_podcasts_by_category(): void
    {
        // Create categories and podcasts
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        
        Podcast::factory()->create(['category_id' => $category1->id]);
        Podcast::factory()->create(['category_id' => $category2->id]);

        // Make the API request
        $response = $this->getJson("/api/podcasts?category={$category1->slug}");

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_podcast_details(): void
    {
        // Create a podcast with category
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->create(['category_id' => $category->id]);

        // Make the API request
        $response = $this->getJson("/api/podcasts/{$podcast->id}");

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'image',
                    'is_featured',
                    'category',
                    'episodes',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
} 