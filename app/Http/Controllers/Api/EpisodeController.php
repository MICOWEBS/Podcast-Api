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
     *     @OA\Response(
     *         response=200,
     *         description="List of episodes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Episode")
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            return EpisodeResource::collection(
                Episode::with('podcast')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
            );
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
            return new EpisodeResource($episode->load('podcast'));
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EpisodeRequest")
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
            return new EpisodeResource($episode->load('podcast'));
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
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EpisodeRequest")
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
            return new EpisodeResource($episode->load('podcast'));
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
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Episode deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
     */
    public function destroy(Episode $episode): \Illuminate\Http\Response
    {
        try {
            $episode->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            throw new ApiException(
                'Failed to delete episode',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
} 