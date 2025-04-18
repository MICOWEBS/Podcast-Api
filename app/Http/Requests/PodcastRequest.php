<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PodcastRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'image' => ['nullable', 'url', 'max:255'],
            'language' => ['required', 'string', 'size:2', 'in:en,es,fr,de,it'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
            'author' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'social_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_links.youtube' => ['nullable', 'url', 'max:255'],
            'explicit' => ['boolean'],
            'is_featured' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required for the podcast.',
            'title.max' => 'The title cannot exceed 255 characters.',
            'description.required' => 'A description is required for the podcast.',
            'description.min' => 'The description must be at least 10 characters long.',
            'image.url' => 'The image must be a valid URL.',
            'language.required' => 'A language code is required.',
            'language.size' => 'The language code must be 2 characters long.',
            'language.in' => 'The language must be one of: en, es, fr, de, it.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.max' => 'Maximum 10 tags are allowed.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'author.required' => 'An author is required for the podcast.',
            'website.url' => 'The website must be a valid URL.',
            'social_links.array' => 'Social links must be provided as an array.',
            'social_links.*.url' => 'Each social link must be a valid URL.',
            'category_id.required' => 'A category is required for the podcast.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }
} 