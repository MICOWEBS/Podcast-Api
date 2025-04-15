<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_episode_details(): void
    {
        // Create a podcast with an episode
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->create(['category_id' => $category->id]);
        $episode = Episode::factory()->create(['podcast_id' => $podcast->id]);

        // Make the API request
        $response = $this->getJson("/api/episodes/{$episode->id}");

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'audio_url',
                    'duration',
                    'podcast',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
} 