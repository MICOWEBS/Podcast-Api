<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Services\CacheService;

class CacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_podcasts_are_cached(): void
    {
        // Create test data
        Podcast::factory()->count(3)->create();

        // Clear cache
        Cache::flush();

        // First request - should hit the database
        $response1 = $this->getJson($this->apiRoute('podcasts'));
        $response1->assertStatus(200);

        // Second request - should hit the cache
        $response2 = $this->getJson($this->apiRoute('podcasts'));
        $response2->assertStatus(200);

        // Verify both responses are identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    public function test_categories_are_cached(): void
    {
        // Create test data
        Category::factory()->count(3)->create();

        // Clear cache
        Cache::flush();

        // First request - should hit the database
        $response1 = $this->getJson($this->apiRoute('categories'));
        $response1->assertStatus(200);

        // Second request - should hit the cache
        $response2 = $this->getJson($this->apiRoute('categories'));
        $response2->assertStatus(200);

        // Verify both responses are identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    public function test_cache_is_invalidated_on_podcast_update(): void
    {
        // Create test data
        $podcast = Podcast::factory()->create();

        // First request to populate cache
        $this->getJson($this->apiRoute('podcasts'));

        // Update podcast
        $this->actingAs($this->user)
            ->putJson($this->apiRoute('podcasts/' . $podcast->id), [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'category_id' => $podcast->category_id
            ]);

        // Make another request
        $response = $this->getJson($this->apiRoute('podcasts'));

        // Verify response contains updated data
        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Updated Title');
    }

    public function test_cache_is_invalidated_on_category_update(): void
    {
        // Create test data
        $category = Category::factory()->create();

        // First request to populate cache
        $this->getJson($this->apiRoute('categories'));

        // Update category
        $this->actingAs($this->user)
            ->putJson($this->apiRoute('categories/' . $category->id), [
                'name' => 'Updated Category',
                'description' => 'Updated Description'
            ]);

        // Make another request
        $response = $this->getJson($this->apiRoute('categories'));

        // Verify response contains updated data
        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Updated Category');
    }

    public function test_cache_ttl(): void
    {
        // Create test data
        Category::factory()->count(3)->create();

        // First request to populate cache
        $this->getJson($this->apiRoute('categories'));

        // Verify cache exists
        $this->assertTrue(Cache::has(CacheService::getCollectionKey(Category::class)));

        // Wait for cache to expire (assuming TTL is 60 seconds)
        sleep(61);

        // Verify cache has expired
        $this->assertFalse(Cache::has(CacheService::getCollectionKey(Category::class)));
    }
} 