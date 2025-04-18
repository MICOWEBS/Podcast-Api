<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PodcastControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    public function test_can_get_all_podcasts(): void
    {
        Podcast::factory()->count(3)->create([
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson($this->apiRoute('podcasts'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'image',
                        'is_featured',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_get_featured_podcasts()
    {
        Podcast::factory()->create(['is_featured' => true, 'category_id' => $this->category->id]);
        Podcast::factory()->create(['is_featured' => false, 'category_id' => $this->category->id]);

        $response = $this->getJson($this->apiRoute('podcasts/featured'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_podcasts_by_category()
    {
        $category = Category::factory()->create();
        Podcast::factory()->create(['category_id' => $category->id]);
        Podcast::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson($this->apiRoute("podcasts/category/{$category->id}"));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_podcast_details(): void
    {
        $podcast = Podcast::factory()->create([
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson($this->apiRoute("podcasts/{$podcast->id}"));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'image',
                    'is_featured',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_podcast()
    {
        $response = $this->getJson($this->apiRoute('podcasts/999'));

        $response->assertStatus(404);
    }

    public function test_can_get_podcast_by_slug()
    {
        $podcast = Podcast::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson($this->apiRoute("podcasts/by-slug/{$podcast->slug}"));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $podcast->id,
                    'title' => $podcast->title,
                    'slug' => $podcast->slug
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_podcast_slug()
    {
        $response = $this->getJson($this->apiRoute('podcasts/by-slug/non-existent-slug'));

        $response->assertStatus(404);
    }
} 