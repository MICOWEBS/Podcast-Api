<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'audio_url',
        'duration',
        'episode_number',
        'season_number',
        'publish_date',
        'explicit',
        'keywords',
        'guests',
        'show_notes',
        'transcript',
        'podcast_id',
    ];

    protected $casts = [
        'duration' => 'integer',
        'episode_number' => 'integer',
        'season_number' => 'integer',
        'publish_date' => 'datetime',
        'explicit' => 'boolean',
        'keywords' => 'array',
        'guests' => 'array',
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }
} 