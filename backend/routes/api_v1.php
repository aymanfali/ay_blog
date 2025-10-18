<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register')
        ->middleware('throttle:5,1'); // 5 requests per minute
        
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login')
        ->middleware('throttle:5,1'); // 5 requests per minute

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'user'])->name('user');
        // Add other protected routes here
    });
});
