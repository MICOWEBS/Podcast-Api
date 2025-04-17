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

    public function test_podcast_validation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Test invalid title
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/podcasts', [
            'title' => 'a', // Too short
            'description' => 'Test description',
            'image_url' => 'invalid-url',
            'category_id' => 999, // Non-existent category
            'language' => 'invalid-language',
            'tags' => ['invalid tag with spaces'],
            'website_url' => 'invalid-url',
            'rss_feed_url' => 'invalid-url'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'image_url',
                'category_id',
                'language',
                'tags.0',
                'website_url',
                'rss_feed_url'
            ]);

        // Test duplicate title
        $category = Category::factory()->create();
        Podcast::factory()->create([
            'title' => 'Existing Podcast',
            'category_id' => $category->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/podcasts', [
            'title' => 'Existing Podcast',
            'description' => 'Test description',
            'image_url' => 'https://example.com/image.jpg',
            'category_id' => $category->id,
            'language' => 'en'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_episode_validation(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $podcast = Podcast::factory()->create();

        // Test invalid episode data
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/episodes', [
            'title' => 'a', // Too short
            'description' => 'Test description',
            'audio_url' => 'invalid-url',
            'duration' => 0, // Invalid duration
            'podcast_id' => 999, // Non-existent podcast
            'episode_number' => -1, // Invalid episode number
            'season_number' => -1, // Invalid season number
            'publish_date' => 'invalid-date',
            'guests' => [
                ['name' => '', 'role' => ''] // Invalid guest data
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'audio_url',
                'duration',
                'podcast_id',
                'episode_number',
                'season_number',
                'publish_date',
                'guests.0.name',
                'guests.0.role'
            ]);

        // Test duplicate episode number
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/episodes', [
            'title' => 'Test Episode',
            'description' => 'Test description',
            'audio_url' => 'https://example.com/audio.mp3',
            'duration' => 300,
            'podcast_id' => $podcast->id,
            'episode_number' => 1,
            'season_number' => 1
        ]);

        $response->assertStatus(201);

        // Try to create another episode with the same number
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/episodes', [
            'title' => 'Another Episode',
            'description' => 'Test description',
            'audio_url' => 'https://example.com/audio2.mp3',
            'duration' => 300,
            'podcast_id' => $podcast->id,
            'episode_number' => 1,
            'season_number' => 1
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['episode_number']);
    }

    public function test_rate_limiting(): void
    {
        // Test login rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password'
            ]);
        }

        $response->assertStatus(429)
            ->assertJson([
                'message' => 'Too Many Attempts.'
            ]);

        // Test password reset rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/forgot-password', [
                'email' => 'test@example.com'
            ]);
        }

        $response->assertStatus(429)
            ->assertJson([
                'message' => 'Too Many Attempts.'
            ]);
    }
} 