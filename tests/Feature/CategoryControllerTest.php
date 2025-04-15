<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_categories(): void
    {
        // Create some categories
        Category::factory()->count(3)->create();

        // Make the API request
        $response = $this->getJson('/api/categories');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'podcasts_count',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }
} 