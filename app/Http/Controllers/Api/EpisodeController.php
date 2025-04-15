<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
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
 *     @OA\Property(property="podcast_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class EpisodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/episodes/{id}",
     *     summary="Get a specific episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of episode",
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
        return new EpisodeResource($episode->load('podcast'));
    }
} 