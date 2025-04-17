<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_podcasts_are_cached(): void
    {
        // Create test data
        $category = Category::factory()->create();
        Podcast::factory()->count(3)->create(['category_id' => $category->id]);

        // Clear cache before test
        Cache::flush();

        // First request - should hit the database
        $response1 = $this->getJson('/api/podcasts');
        $response1->assertStatus(200);

        // Second request - should hit the cache
        $response2 = $this->getJson('/api/podcasts');
        $response2->assertStatus(200);

        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());

        // Verify cache key exists
        $this->assertTrue(Cache::has('podcasts:page:1'));
    }

    public function test_categories_are_cached(): void
    {
        // Create test data
        Category::factory()->count(3)->create();

        // Clear cache before test
        Cache::flush();

        // First request - should hit the database
        $response1 = $this->getJson('/api/categories');
        $response1->assertStatus(200);

        // Second request - should hit the cache
        $response2 = $this->getJson('/api/categories');
        $response2->assertStatus(200);

        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());

        // Verify cache key exists
        $this->assertTrue(Cache::has('categories:all'));
    }

    public function test_cache_is_invalidated_on_podcast_update(): void
    {
        // Create test data
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->create(['category_id' => $category->id]);

        // Clear cache before test
        Cache::flush();

        // First request to populate cache
        $this->getJson('/api/podcasts');

        // Update podcast
        $podcast->update(['title' => 'Updated Title']);

        // Cache should be invalidated
        $this->assertFalse(Cache::has('podcasts:page:1'));
    }

    public function test_cache_is_invalidated_on_category_update(): void
    {
        // Create test data
        $category = Category::factory()->create();

        // Clear cache before test
        Cache::flush();

        // First request to populate cache
        $this->getJson('/api/categories');

        // Update category
        $category->update(['name' => 'Updated Category']);

        // Cache should be invalidated
        $this->assertFalse(Cache::has('categories:all'));
    }

    public function test_cache_ttl(): void
    {
        // Create test data
        Category::factory()->create();

        // Clear cache before test
        Cache::flush();

        // First request to populate cache
        $this->getJson('/api/categories');

        // Verify cache exists
        $this->assertTrue(Cache::has('categories:all'));

        // Wait for cache to expire (assuming TTL is 60 seconds)
        sleep(61);

        // Cache should be expired
        $this->assertFalse(Cache::has('categories:all'));
    }
} 