<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        
        $this->category = Category::factory()->create();
        $this->podcast = Podcast::factory()->create([
            'category_id' => $this->category->id
        ]);
    }

    public function test_can_get_all_episodes(): void
    {
        // Create test data
        Episode::factory()->count(3)->create([
            'podcast_id' => $this->podcast->id
        ]);

        // Make the API request
        $response = $this->getJson($this->apiRoute('episodes'));

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
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
                        'transcript',
                        'podcast' => [
                            'id',
                            'title',
                            'description',
                            'image',
                            'is_featured'
                        ],
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_get_episode_details(): void
    {
        // Create test data
        $episode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id
        ]);

        // Make the API request
        $response = $this->getJson($this->apiRoute('episodes/' . $episode->id));

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
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
                    'transcript',
                    'podcast' => [
                        'id',
                        'title',
                        'description',
                        'image',
                        'is_featured'
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_can_create_episode(): void
    {
        // Prepare test data
        $episodeData = [
            'title' => 'Test Episode',
            'description' => 'Test Description',
            'audio_url' => 'https://example.com/audio.mp3',
            'duration' => 1800,
            'podcast_id' => $this->podcast->id,
            'episode_number' => 1,
            'season_number' => 1,
            'publish_date' => now()->toDateString(),
            'explicit' => false,
            'keywords' => ['test', 'episode'],
            'guests' => [
                ['name' => 'John Doe', 'role' => 'Host']
            ],
            'show_notes' => 'Test show notes',
            'transcript' => 'Test transcript'
        ];

        // Make the API request
        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('episodes'), $episodeData);

        // Assert response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
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
                    'transcript',
                    'podcast' => [
                        'id',
                        'title',
                        'description',
                        'image',
                        'is_featured'
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Assert database
        $this->assertDatabaseHas('episodes', [
            'title' => 'Test Episode',
            'podcast_id' => $this->podcast->id
        ]);
    }

    public function test_can_update_episode(): void
    {
        // Create test data
        $episode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id
        ]);

        // Prepare update data
        $updateData = [
            'title' => 'Updated Episode Title',
            'description' => 'Updated episode description',
            'audio_url' => 'https://example.com/updated-audio.mp3',
            'duration' => 2400,
            'episode_number' => $episode->episode_number,
            'season_number' => $episode->season_number,
            'publish_date' => now()->toDateString(),
            'podcast_id' => $this->podcast->id,
            'explicit' => false,
            'keywords' => ['updated', 'episode'],
            'guests' => [
                ['name' => 'Jane Doe', 'role' => 'Guest']
            ],
            'show_notes' => 'Updated show notes',
            'transcript' => 'Updated transcript'
        ];

        // Make the API request
        $response = $this->actingAs($this->user)
            ->putJson($this->apiRoute('episodes/' . $episode->id), $updateData);

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
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
                    'transcript',
                    'podcast' => [
                        'id',
                        'title',
                        'description',
                        'image',
                        'is_featured'
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Assert database
        $this->assertDatabaseHas('episodes', [
            'id' => $episode->id,
            'title' => 'Updated Episode Title'
        ]);
    }

    public function test_can_delete_episode(): void
    {
        // Create test data
        $episode = Episode::factory()->create([
            'podcast_id' => $this->podcast->id
        ]);

        // Make the API request
        $response = $this->actingAs($this->user)
            ->deleteJson($this->apiRoute('episodes/' . $episode->id));

        // Assert response
        $response->assertStatus(204);

        // Assert database
        $this->assertDatabaseMissing('episodes', [
            'id' => $episode->id
        ]);
    }

    public function test_returns_404_for_non_existent_episode(): void
    {
        // Make the API request
        $response = $this->getJson($this->apiRoute('episodes/999'));

        // Assert response
        $response->assertStatus(404);
    }

    public function test_validates_required_fields_on_create(): void
    {
        // Make the API request with missing required fields
        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('episodes'), []);

        // Assert response
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
                'audio_url',
                'duration',
                'podcast_id',
                'episode_number',
                'season_number',
                'publish_date'
            ]);
    }

    public function test_validates_episode_number_uniqueness(): void
    {
        // Create an episode with episode number 1
        Episode::factory()->create([
            'podcast_id' => $this->podcast->id,
            'episode_number' => 1
        ]);

        // Try to create another episode with the same number
        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('episodes'), [
                'title' => 'Test Episode',
                'description' => 'Test Description',
                'audio_url' => 'https://example.com/audio.mp3',
                'duration' => 1800,
                'podcast_id' => $this->podcast->id,
                'episode_number' => 1,
                'season_number' => 1,
                'publish_date' => now()->toDateString()
            ]);

        // Assert response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['episode_number']);
    }
} 