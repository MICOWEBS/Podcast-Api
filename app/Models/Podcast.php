<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'is_featured',
        'category_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($podcast) {
            $podcast->slug = Str::slug($podcast->title);
        });

        static::updating(function ($podcast) {
            if ($podcast->isDirty('title')) {
                $podcast->slug = Str::slug($podcast->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
} 