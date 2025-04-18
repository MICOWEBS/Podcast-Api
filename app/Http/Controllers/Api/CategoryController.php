<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Exceptions\ApiException;
use App\Services\CacheService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for podcast categories"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="podcasts_count", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = CacheService::remember(
            CacheService::getCollectionKey(Category::class),
            fn () => Category::all()
        );
        
        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{category}",
     *     summary="Get category details",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function show(Category $category): CategoryResource
    {
        $cacheKey = CacheService::getModelKey($category);
        return new CategoryResource(
            CacheService::remember($cacheKey, fn () => $category)
        );
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());
        CacheService::clearModelTypeCache(Category::class);
        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());
        CacheService::clearModelCache($category);
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->podcasts()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with associated podcasts'
            ], 422);
        }

        $category->delete();
        CacheService::clearModelCache($category);
        
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    public function findBySlug($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return new CategoryResource($category);
    }
} 