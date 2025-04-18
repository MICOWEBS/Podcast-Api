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
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="is_featured", type="boolean"),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="episodes_count", type="integer"),
 *     @OA\Property(property="episodes", type="array", @OA\Items(ref="#/components/schemas/Episode")),
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
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Podcast")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Podcast::query();
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }
        
        $cacheKey = CacheService::getCollectionKey(Podcast::class, $request->all());
        $podcasts = CacheService::remember($cacheKey, fn () => $query->paginate());
        
        return PodcastResource::collection($podcasts);
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
     */
    public function show(Podcast $podcast): PodcastResource
    {
        $cacheKey = CacheService::getModelKey($podcast);
        return new PodcastResource(
            CacheService::remember($cacheKey, fn () => $podcast->load('episodes'))
        );
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

    /**
     * @OA\Get(
     *     path="/api/podcasts/featured",
     *     summary="Get featured podcasts",
     *     tags={"Podcasts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of featured podcasts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Podcast")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function featured(): AnonymousResourceCollection
    {
        $podcasts = Podcast::with('category')
            ->where('is_featured', true)
            ->latest()
            ->get();

        return PodcastResource::collection($podcasts);
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/category/{category}",
     *     summary="Get podcasts by category",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of podcasts in category",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Podcast")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function byCategory(Category $category): AnonymousResourceCollection
    {
        $podcasts = $category->podcasts()
            ->with('category')
            ->latest()
            ->paginate(10);

        return PodcastResource::collection($podcasts);
    }

    public function showBySlug(string $slug): PodcastResource
    {
        $cacheKey = CacheService::getCollectionKey(Podcast::class, ['slug' => $slug]);
        $podcast = CacheService::remember($cacheKey, function () use ($slug) {
            return Podcast::where('slug', $slug)
                ->with('episodes')
                ->firstOrFail();
        });
        
        return new PodcastResource($podcast);
    }

    public function store(Request $request): PodcastResource
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:podcasts',
            'description' => 'required|string',
            'image' => 'nullable|url',
            'is_featured' => 'boolean',
            'category_id' => 'required|exists:categories,id'
        ]);

        $podcast = Podcast::create($validated);
        CacheService::clearModelTypeCache(Podcast::class);
        return new PodcastResource($podcast->load('category'));
    }

    public function update(Request $request, Podcast $podcast): PodcastResource
    {
        $validated = $request->validate([
            'title' => 'string|max:255|unique:podcasts,title,' . $podcast->id,
            'description' => 'string',
            'image' => 'nullable|url',
            'is_featured' => 'boolean',
            'category_id' => 'exists:categories,id'
        ]);

        $podcast->update($validated);
        CacheService::clearModelCache($podcast);
        return new PodcastResource($podcast->load('category'));
    }

    public function destroy(Podcast $podcast): Response
    {
        $podcast->delete();
        CacheService::clearModelCache($podcast);

        return response()->noContent();
    }
} 