<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function podcasts(): HasMany
    {
        return $this->hasMany(Podcast::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saved(function ($category) {
            Cache::forget('categories:all');
            Cache::forget("categories:{$category->id}");
        });

        static::deleted(function ($category) {
            Cache::forget('categories:all');
            Cache::forget("categories:{$category->id}");
        });
    }
} 