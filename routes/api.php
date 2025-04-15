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

Route::prefix('api')->group(function () {
    // Public routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Password reset routes
    Route::post('auth/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [PasswordResetController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Categories
        Route::get('categories', [CategoryController::class, 'index']);

        // Podcasts
        Route::get('podcasts', [PodcastController::class, 'index']);
        Route::get('podcasts/{podcast}', [PodcastController::class, 'show']);
        Route::get('podcasts/{podcast}/episodes', [PodcastController::class, 'episodes']);

        // Episodes
        Route::get('episodes/{episode}', [EpisodeController::class, 'show']);
    });
}); 