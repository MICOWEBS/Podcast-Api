<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class PodcastRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('podcasts')->ignore($this->podcast),
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'image_url' => [
                'required',
                'url',
                'max:2048',
                'regex:/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp)$/i',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'is_featured' => [
                'boolean',
            ],
            'tags' => [
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
            'language' => [
                'required',
                'string',
                'size:2',
                'in:en,es,fr,de,it,pt,ru,zh,ja,ko',
            ],
            'explicit' => [
                'boolean',
            ],
            'author' => [
                'required',
                'string',
                'max:255',
            ],
            'website' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'social_links' => [
                'array',
                'max:5',
            ],
            'social_links.*.platform' => [
                'required_with:social_links',
                'string',
                'in:twitter,facebook,instagram,youtube,spotify',
            ],
            'social_links.*.url' => [
                'required_with:social_links',
                'url',
                'max:2048',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The podcast title is required.',
            'title.min' => 'The podcast title must be at least 3 characters.',
            'title.unique' => 'This podcast title is already taken.',
            'description.required' => 'The podcast description is required.',
            'description.min' => 'The podcast description must be at least 10 characters.',
            'image_url.required' => 'The podcast image URL is required.',
            'image_url.url' => 'The podcast image must be a valid URL.',
            'image_url.regex' => 'The podcast image must be a valid image file (jpg, jpeg, png, gif, or webp).',
            'category_id.required' => 'The podcast category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'tags.max' => 'You can add up to 10 tags.',
            'tags.*.regex' => 'Tags can only contain letters, numbers, spaces, hyphens, and underscores.',
            'language.required' => 'The podcast language is required.',
            'language.in' => 'The selected language is not supported.',
            'author.required' => 'The podcast author is required.',
            'website.url' => 'The website must be a valid URL.',
            'social_links.*.platform.in' => 'The selected social media platform is not supported.',
            'social_links.*.url.url' => 'The social media URL must be a valid URL.',
        ];
    }
} 