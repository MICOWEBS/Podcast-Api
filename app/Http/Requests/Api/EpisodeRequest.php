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
        $episodeId = $this->route('episode')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'audio_url' => ['required', 'url', 'regex:/\.(mp3|wav|m4a)$/i'],
            'duration' => ['required', 'integer', 'min:1', 'max:86400'],
            'episode_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('episodes')->where(function ($query) {
                    return $query->where('podcast_id', $this->input('podcast_id'));
                })->ignore($episodeId)
            ],
            'season_number' => ['required', 'integer', 'min:1'],
            'publish_date' => ['required', 'date'],
            'explicit' => ['boolean'],
            'keywords' => ['array', 'max:10'],
            'keywords.*' => ['string', 'max:50'],
            'guests' => ['array', 'max:10'],
            'guests.*.name' => ['required_with:guests', 'string', 'max:255'],
            'guests.*.role' => ['required_with:guests', 'string', 'max:255'],
            'show_notes' => ['string', 'max:10000'],
            'transcript' => ['string', 'max:50000'],
            'podcast_id' => ['required', 'exists:podcasts,id'],
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