<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Exceptions\ApiException;
use App\Services\CacheService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

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
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $cacheKey = CacheService::getCollectionKey(Category::class);
            
            return CacheService::remember($cacheKey, function () {
                return CategoryResource::collection(
                    Category::orderBy('name', 'asc')->get()
                );
            });
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch categories',
                ['error' => $e->getMessage()],
                500
            );
        }
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
        try {
            $cacheKey = CacheService::getModelKey($category);
            
            return CacheService::remember($cacheKey, function () use ($category) {
                return new CategoryResource($category);
            });
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch category details',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
} 