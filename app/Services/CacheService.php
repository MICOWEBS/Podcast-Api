<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CacheService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 5;

    /**
     * Get cached data or store the result of the callback
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $duration
     * @return mixed
     */
    public static function remember(string $key, callable $callback, ?int $duration = null)
    {
        return Cache::remember($key, now()->addMinutes($duration ?? self::CACHE_DURATION), $callback);
    }

    /**
     * Generate a cache key for a model
     *
     * @param Model $model
     * @return string
     */
    public static function getModelKey(Model $model): string
    {
        return strtolower(class_basename($model)) . ":{$model->id}";
    }

    /**
     * Generate a cache key for a collection
     *
     * @param string $model
     * @param array $filters
     * @return string
     */
    public static function getCollectionKey(string $model, array $filters = []): string
    {
        $key = strtolower(class_basename($model)) . 's';
        if (!empty($filters)) {
            $key .= ':' . md5(json_encode($filters));
        }
        return $key;
    }

    /**
     * Clear cache for a model and its relations
     *
     * @param Model $model
     * @return void
     */
    public static function clearModelCache(Model $model): void
    {
        // Clear model cache
        Cache::forget(self::getModelKey($model));

        // Clear collection cache
        Cache::forget(self::getCollectionKey(get_class($model)));

        // Clear related caches
        if (method_exists($model, 'getRelations')) {
            foreach ($model->getRelations() as $relation) {
                if ($relation instanceof Model) {
                    self::clearModelCache($relation);
                }
            }
        }
    }

    /**
     * Clear all caches for a model type
     *
     * @param string $model
     * @return void
     */
    public static function clearModelTypeCache(string $model): void
    {
        $pattern = strtolower(class_basename($model)) . '*';
        Cache::getMultiple([$pattern]);
    }

    /**
     * Clear all caches
     *
     * @return void
     */
    public static function clearAllCaches(): void
    {
        Cache::flush();
    }
} 