<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,
            'is_featured' => $this->is_featured,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'episodes_count' => $this->whenCounted('episodes'),
            'episodes' => EpisodeResource::collection($this->whenLoaded('episodes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 