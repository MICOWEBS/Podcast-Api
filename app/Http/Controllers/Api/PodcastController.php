<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ListPodcastRequest;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\PodcastResource;
use App\Models\Category;
use App\Models\Podcast;
use App\Exceptions\ApiException;
use App\Services\CacheService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Podcasts",
 *     description="API Endpoints for podcasts"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Podcast",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="image_url", type="string"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class PodcastController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/podcasts",
     *     summary="Get all podcasts",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter featured podcasts",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category slug",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of podcasts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="code", type="integer", example=400)
     *         )
     *     )
     * )
     */
    public function index(ListPodcastRequest $request): AnonymousResourceCollection
    {
        try {
            $filters = $request->only(['featured', 'category']);
            $cacheKey = CacheService::getCollectionKey(Podcast::class, $filters);
            
            return CacheService::remember($cacheKey, function () use ($request) {
                $query = Podcast::with(['category', 'episodes']);

                if ($request->boolean('featured')) {
                    $query->where('is_featured', true);
                }

                if ($request->has('category')) {
                    try {
                        $category = Category::where('slug', $request->category)->firstOrFail();
                        $query->where('category_id', $category->id);
                    } catch (\Exception $e) {
                        throw new ApiException(
                            'Category not found',
                            ['category' => ['The specified category does not exist']],
                            404
                        );
                    }
                }

                if ($request->has('search')) {
                    $query->where(function($q) use ($request) {
                        $q->where('title', 'like', '%' . $request->search . '%')
                          ->orWhere('description', 'like', '%' . $request->search . '%');
                    });
                }

                if ($request->has('sort')) {
                    $allowedSorts = ['latest', 'oldest', 'title'];
                    if (!in_array($request->sort, $allowedSorts)) {
                        throw new ApiException(
                            'Invalid sort parameter',
                            ['sort' => ['The sort parameter must be one of: ' . implode(', ', $allowedSorts)]],
                            400
                        );
                    }

                    switch ($request->sort) {
                        case 'latest':
                            $query->latest();
                            break;
                        case 'oldest':
                            $query->oldest();
                            break;
                        case 'title':
                            $query->orderBy('title');
                            break;
                    }
                }

                $perPage = $request->input('per_page', 12);
                if ($perPage < 1 || $perPage > 100) {
                    throw new ApiException(
                        'Invalid per_page parameter',
                        ['per_page' => ['The per_page parameter must be between 1 and 100']],
                        400
                    );
                }

                $podcasts = $query->paginate($perPage);
                
                if ($podcasts->isEmpty()) {
                    throw new ApiException(
                        'No podcasts found',
                        [],
                        404
                    );
                }
                
                return PodcastResource::collection($podcasts);
            });
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch podcasts',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/{podcast}",
     *     summary="Get podcast details",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="podcast",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast details",
     *         @OA\JsonContent(ref="#/components/schemas/Podcast")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
     */
    public function show(Podcast $podcast): PodcastResource
    {
        try {
            $cacheKey = CacheService::getModelKey($podcast);
            
            return CacheService::remember($cacheKey, function () use ($podcast) {
                return new PodcastResource(
                    $podcast->load(['category', 'episodes'])
                );
            });
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch podcast details',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/{podcast}/episodes",
     *     summary="Get podcast episodes",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="podcast",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="season",
     *         in="query",
     *         description="Filter by season number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of episodes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
     */
    public function episodes(Podcast $podcast): AnonymousResourceCollection
    {
        try {
            $filters = ['podcast_id' => $podcast->id];
            if (request()->has('season')) {
                $filters['season'] = request('season');
            }
            
            $cacheKey = CacheService::getCollectionKey('Episode', $filters);
            
            return CacheService::remember($cacheKey, function () use ($podcast) {
                $query = $podcast->episodes();
                
                if (request()->has('season')) {
                    $query->where('season_number', request('season'));
                }
                
                return EpisodeResource::collection(
                    $query->orderBy('season_number', 'desc')
                          ->orderBy('episode_number', 'desc')
                          ->paginate(10)
                );
            });
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch podcast episodes',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
} 