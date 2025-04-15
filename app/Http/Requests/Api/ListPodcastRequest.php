<?php

namespace App\Http\Requests\Api;

class ListPodcastRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'featured' => 'sometimes|boolean',
            'category' => 'sometimes|string|exists:categories,slug',
            'search' => 'sometimes|string|min:3',
            'sort' => 'sometimes|string|in:latest,oldest,title',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ];
    }
} 