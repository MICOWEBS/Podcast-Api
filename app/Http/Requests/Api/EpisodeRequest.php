<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class EpisodeRequest extends ApiRequest
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
                Rule::unique('episodes')->where(function ($query) {
                    return $query->where('podcast_id', $this->podcast_id);
                })->ignore($this->episode),
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'audio_url' => [
                'required',
                'url',
                'max:2048',
                'regex:/^https?:\/\/.+\.(mp3|m4a|wav|ogg)$/i',
            ],
            'duration' => [
                'required',
                'integer',
                'min:1',
                'max:86400', // 24 hours in seconds
            ],
            'podcast_id' => [
                'required',
                'integer',
                'exists:podcasts,id',
            ],
            'episode_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('episodes')->where(function ($query) {
                    return $query->where('podcast_id', $this->podcast_id);
                })->ignore($this->episode),
            ],
            'season_number' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'publish_date' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'explicit' => [
                'boolean',
            ],
            'keywords' => [
                'array',
                'max:20',
            ],
            'keywords.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
            'guests' => [
                'array',
                'max:10',
            ],
            'guests.*.name' => [
                'required_with:guests',
                'string',
                'max:255',
            ],
            'guests.*.role' => [
                'required_with:guests',
                'string',
                'max:255',
            ],
            'show_notes' => [
                'nullable',
                'string',
                'max:5000,
            ],
            'transcript' => [
                'nullable',
                'string',
                'max:50000,
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
            'title.required' => 'The episode title is required.',
            'title.min' => 'The episode title must be at least 3 characters.',
            'title.unique' => 'This episode title is already taken for this podcast.',
            'description.required' => 'The episode description is required.',
            'description.min' => 'The episode description must be at least 10 characters.',
            'audio_url.required' => 'The episode audio URL is required.',
            'audio_url.url' => 'The episode audio must be a valid URL.',
            'audio_url.regex' => 'The episode audio must be a valid audio file (mp3, m4a, wav, or ogg).',
            'duration.required' => 'The episode duration is required.',
            'duration.min' => 'The episode duration must be at least 1 second.',
            'duration.max' => 'The episode duration cannot exceed 24 hours.',
            'podcast_id.required' => 'The podcast ID is required.',
            'podcast_id.exists' => 'The selected podcast does not exist.',
            'episode_number.required' => 'The episode number is required.',
            'episode_number.min' => 'The episode number must be at least 1.',
            'episode_number.unique' => 'This episode number is already taken for this podcast.',
            'season_number.min' => 'The season number must be at least 1.',
            'publish_date.before_or_equal' => 'The publish date cannot be in the future.',
            'keywords.max' => 'You can add up to 20 keywords.',
            'keywords.*.regex' => 'Keywords can only contain letters, numbers, spaces, hyphens, and underscores.',
            'guests.max' => 'You can add up to 10 guests.',
            'guests.*.name.required_with' => 'Guest name is required when adding guests.',
            'guests.*.role.required_with' => 'Guest role is required when adding guests.',
            'show_notes.max' => 'Show notes cannot exceed 5000 characters.',
            'transcript.max' => 'Transcript cannot exceed 50000 characters.',
        ];
    }
} 