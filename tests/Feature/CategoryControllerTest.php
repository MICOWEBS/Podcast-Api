<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson($this->apiRoute('categories'));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_get_category_details(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson($this->apiRoute('categories/' . $category->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_category()
    {
        $response = $this->getJson($this->apiRoute('categories/999'));

        $response->assertStatus(404);
    }

    public function test_can_create_category(): void
    {
        $categoryData = [
            'name' => 'Test Category',
            'description' => 'Test Description'
        ];

        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('categories'), $categoryData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();
        $updateData = [
            'name' => 'Updated Category',
            'description' => 'Updated Description'
        ];

        $response = $this->actingAs($this->user)
            ->putJson($this->apiRoute('categories/' . $category->id), $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('categories', $updateData);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson($this->apiRoute('categories/' . $category->id));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category deleted successfully'
            ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_podcasts(): void
    {
        $category = Category::factory()->create();
        Podcast::factory()->create([
            'category_id' => $category->id,
            'title' => 'Test Podcast',
            'description' => 'Test Description',
            'author' => 'Test Author'
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson($this->apiRoute('categories/' . $category->id));

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot delete category with associated podcasts'
            ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('categories'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_validates_name_uniqueness()
    {
        $existingCategory = Category::factory()->create();
        
        $response = $this->actingAs($this->user)
            ->postJson($this->apiRoute('categories'), [
                'name' => $existingCategory->name,
                'description' => $this->faker->sentence
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
} 