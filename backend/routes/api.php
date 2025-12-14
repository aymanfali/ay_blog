<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController,
    BookmarkController,
    CategoryController,
    CommentController,
    FallbackController,
    PostController,
    ReactionController,
    SettingController,
    TagController,
    UserController,
};

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {

    // authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
        });
    });

    // protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('posts', PostController::class);
        Route::apiResource('tags', TagController::class);
        Route::apiResource('comments', CommentController::class);
        Route::apiResource('reactions', ReactionController::class);
        Route::apiResource('bookmarks', BookmarkController::class)->except(['update']);
        Route::apiResource('settings', SettingController::class);
        Route::apiResource('users', UserController::class);
    });

    // fallback route
    Route::fallback(FallbackController::class);
});
