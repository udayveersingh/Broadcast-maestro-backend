<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\API\TargetAudienceController;
use App\Http\Controllers\Api\ToolController;
use App\Http\Controllers\Api\ToolParameterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        // Route::get('profile', [AuthController::class, 'profile']);
        // Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::post('refresh', [AuthController::class, 'refresh']);

        Route::get('/profile', [ProfileApiController::class, 'show']);
        Route::post('/profile', [ProfileApiController::class, 'update']);
    });

    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::get('/goals', [GoalController::class, 'index']);
    Route::get('/target-audiences', [TargetAudienceController::class, 'index']);
    Route::get('/tools', [ToolController::class, 'index']);
});