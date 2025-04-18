<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'audio_url' => $this->audio_url,
            'duration' => $this->duration,
            'episode_number' => $this->episode_number,
            'season_number' => $this->season_number,
            'publish_date' => $this->publish_date,
            'explicit' => $this->explicit,
            'keywords' => $this->keywords,
            'guests' => $this->guests,
            'show_notes' => $this->show_notes,
            'transcript' => $this->transcript,
            'podcast' => new PodcastResource($this->whenLoaded('podcast')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 