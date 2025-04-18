<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PodcastController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Public routes
    Route::get('podcasts', [PodcastController::class, 'index']);
    Route::get('podcasts/featured', [PodcastController::class, 'featured']);
    Route::get('podcasts/category/{category}', [PodcastController::class, 'byCategory']);
    Route::get('podcasts/by-slug/{slug}', [PodcastController::class, 'showBySlug']);
    Route::get('podcasts/{podcast}', [PodcastController::class, 'show']);

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/by-slug/{slug}', [CategoryController::class, 'findBySlug']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);

    Route::get('episodes', [EpisodeController::class, 'index']);
    Route::get('episodes/{episode}', [EpisodeController::class, 'show']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Protected podcast routes
        Route::post('podcasts', [PodcastController::class, 'store']);
        Route::put('podcasts/{podcast}', [PodcastController::class, 'update']);
        Route::delete('podcasts/{podcast}', [PodcastController::class, 'destroy']);

        // Protected category routes
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        // Protected episode routes
        Route::post('episodes', [EpisodeController::class, 'store']);
        Route::put('episodes/{episode}', [EpisodeController::class, 'update']);
        Route::delete('episodes/{episode}', [EpisodeController::class, 'destroy']);
    });
}); 