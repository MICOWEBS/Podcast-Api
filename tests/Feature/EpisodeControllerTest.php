<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EpisodeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $podcast;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->podcast = Podcast::factory()->create();
    }

    public function test_can_get_all_episodes()
    {
        Episode::factory()->count(3)->create([
            'podcast_id' => $this->podcast->id
        ]);

        $response = $this->getJson('/api/episodes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'episode_number',
                        'season_number',
                        'duration',
                        'audio_url',
                        'publish_date',
                        'podcast' => [
                            'id',
                            'title'
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_get_episode_details()
    {
        $episode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id
        ]);

        $response = $this->getJson("/api/episodes/{$episode->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'episode_number',
                    'season_number',
                    'duration',
                    'audio_url',
                    'publish_date',
                    'podcast' => [
                        'id',
                        'title'
                    ]
                ]
            ]);
    }

    public function test_can_create_episode()
    {
        $this->actingAs($this->user);

        $episodeData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'episode_number' => 1,
            'season_number' => 1,
            'duration' => 1800,
            'audio_url' => $this->faker->url,
            'publish_date' => now()->toDateTimeString(),
            'podcast_id' => $this->podcast->id
        ];

        $response = $this->postJson('/api/episodes', $episodeData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'episode_number',
                    'season_number',
                    'duration',
                    'audio_url',
                    'publish_date',
                    'podcast' => [
                        'id',
                        'title'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('episodes', [
            'title' => $episodeData['title'],
            'podcast_id' => $this->podcast->id
        ]);
    }

    public function test_can_update_episode()
    {
        $this->actingAs($this->user);

        $episode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id
        ]);

        $updateData = [
            'title' => 'Updated Episode Title',
            'description' => 'Updated description'
        ];

        $response = $this->putJson("/api/episodes/{$episode->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => $updateData['title'],
                    'description' => $updateData['description']
                ]
            ]);

        $this->assertDatabaseHas('episodes', [
            'id' => $episode->id,
            'title' => $updateData['title']
        ]);
    }

    public function test_can_delete_episode()
    {
        $this->actingAs($this->user);

        $episode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id
        ]);

        $response = $this->deleteJson("/api/episodes/{$episode->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('episodes', [
            'id' => $episode->id
        ]);
    }

    public function test_returns_404_for_non_existent_episode()
    {
        $response = $this->getJson('/api/episodes/99999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Episode not found'
            ]);
    }

    public function test_validates_required_fields_on_create()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/episodes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'episode_number', 'podcast_id']);
    }

    public function test_validates_episode_number_uniqueness()
    {
        $this->actingAs($this->user);

        $existingEpisode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id,
            'episode_number' => 1
        ]);

        $response = $this->postJson('/api/episodes', [
            'title' => $this->faker->sentence,
            'episode_number' => 1,
            'podcast_id' => $this->podcast->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['episode_number']);
    }
} 