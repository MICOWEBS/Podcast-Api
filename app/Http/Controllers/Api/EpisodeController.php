<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use App\Models\Podcast;
use App\Exceptions\ApiException;
use App\Services\CacheService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Episodes",
 *     description="API Endpoints for podcast episodes"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Episode",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="audio_url", type="string"),
 *     @OA\Property(property="duration", type="integer"),
 *     @OA\Property(property="episode_number", type="integer"),
 *     @OA\Property(property="season_number", type="integer"),
 *     @OA\Property(property="publish_date", type="string", format="date-time"),
 *     @OA\Property(property="explicit", type="boolean"),
 *     @OA\Property(property="keywords", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="guests", type="array", @OA\Items(
 *         type="object",
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="role", type="string")
 *     )),
 *     @OA\Property(property="show_notes", type="string"),
 *     @OA\Property(property="transcript", type="string"),
 *     @OA\Property(property="podcast", ref="#/components/schemas/Podcast"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class EpisodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/episodes",
     *     summary="Get all episodes",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="podcast_id",
     *         in="query",
     *         description="Filter by podcast ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="season",
     *         in="query",
     *         description="Filter by season number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search episodes by title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of episodes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Episode")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="per_page", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $filters = request()->only(['podcast_id', 'season', 'search']);
            $cacheKey = CacheService::getCollectionKey(Episode::class, $filters);
            
            return CacheService::remember($cacheKey, function () use ($filters) {
                $query = Episode::with(['podcast']);

                if (isset($filters['podcast_id'])) {
                    $query->where('podcast_id', $filters['podcast_id']);
                }

                if (isset($filters['season'])) {
                    $query->where('season_number', $filters['season']);
                }

                if (isset($filters['search'])) {
                    $search = $filters['search'];
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                }

                return EpisodeResource::collection(
                    $query->orderBy('season_number', 'desc')
                          ->orderBy('episode_number', 'desc')
                          ->paginate(10)
                );
            });
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch episodes',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/episodes/{episode}",
     *     summary="Get episode details",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode details",
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
     */
    public function show(Episode $episode): EpisodeResource
    {
        try {
            $cacheKey = CacheService::getModelKey($episode);
            
            return CacheService::remember($cacheKey, function () use ($episode) {
                return new EpisodeResource(
                    $episode->load(['podcast'])
                );
            });
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to fetch episode details',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/episodes",
     *     summary="Create new episode",
     *     tags={"Episodes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Episode created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(EpisodeRequest $request): EpisodeResource
    {
        try {
            $episode = Episode::create($request->validated());
            
            // Clear related caches
            CacheService::clearModelCache($episode);
            CacheService::clearModelTypeCache(Episode::class);
            
            return new EpisodeResource($episode->load(['podcast']));
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to create episode',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * @OA\Put(
     *     path="/api/episodes/{episode}",
     *     summary="Update episode",
     *     tags={"Episodes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
     */
    public function update(EpisodeRequest $request, Episode $episode): EpisodeResource
    {
        try {
            $episode->update($request->validated());
            
            // Clear related caches
            CacheService::clearModelCache($episode);
            CacheService::clearModelTypeCache(Episode::class);
            
            return new EpisodeResource($episode->load(['podcast']));
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to update episode',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/episodes/{episode}",
     *     summary="Delete episode",
     *     tags={"Episodes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
     */
    public function destroy(Episode $episode): \Illuminate\Http\JsonResponse
    {
        try {
            $podcastId = $episode->podcast_id;
            $episode->delete();
            
            // Clear related caches
            CacheService::clearModelCache($episode);
            CacheService::clearModelTypeCache(Episode::class);
            
            return response()->json([
                'message' => 'Episode deleted successfully'
            ]);
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to delete episode',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
} 