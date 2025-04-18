<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    public function test_podcast_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('podcasts'), [
                'title' => '', // Empty title
                'description' => '', // Empty description
                'image' => 'invalid-url', // Invalid image URL
                'category_id' => 999, // Non-existent category
                'author' => 'Test Author', // Required author field
                'rss_feed_url' => 'invalid-url' // Invalid RSS feed URL
            ]);

        // Assert response
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
                'image',
                'category_id'
            ]);
    }

    public function test_episode_validation(): void
    {
        // Create a podcast first
        $podcast = Podcast::factory()->create([
            'category_id' => $this->category->id,
            'author' => 'Test Author'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('episodes'), [
                'title' => '', // Empty title
                'description' => '', // Empty description
                'audio_url' => 'invalid-url', // Invalid audio URL
                'duration' => -1, // Invalid duration
                'podcast_id' => $podcast->id,
                'episode_number' => 0, // Invalid episode number
                'season_number' => 0, // Invalid season number
                'publish_date' => 'invalid-date', // Invalid date
                'explicit' => 'invalid', // Invalid boolean
                'keywords' => 'not-an-array', // Invalid keywords format
                'guests' => 'not-an-array', // Invalid guests format
                'show_notes' => str_repeat('a', 10001), // Too long show notes
                'transcript' => str_repeat('a', 50001) // Too long transcript
            ]);

        // Assert response
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
                'audio_url',
                'duration',
                'episode_number',
                'season_number',
                'publish_date',
                'explicit',
                'keywords',
                'guests',
                'show_notes',
                'transcript'
            ]);
    }

    public function test_rate_limiting(): void
    {
        // Make multiple requests to a protected endpoint
        for ($i = 0; $i < 61; $i++) {
            $response = $this->actingAs($this->user)
                ->postJson($this->apiRoute('podcasts'), [
                    'title' => 'Test Podcast',
                    'description' => 'Test Description',
                    'image' => 'https://example.com/image.jpg',
                    'category_id' => $this->category->id,
                    'author' => 'Test Author'
                ]);
        }

        // Assert response
        $response->assertStatus(429)
            ->assertJson([
                'message' => 'Too Many Attempts.'
            ]);
    }
} 