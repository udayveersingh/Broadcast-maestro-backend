<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\API\TargetAudienceController;
use App\Http\Controllers\Api\ToolController;
use App\Http\Controllers\Api\ToolParameterController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('social-login/google', [AuthController::class, 'googleLogin']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);

    Route::post('/send-reset-code', [ForgotPasswordController::class, 'sendResetCode']);
    Route::post('/reset-password-with-code', [ForgotPasswordController::class, 'resetWithCode']);

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
    Route::get('/my-campaigns', [CampaignController::class, 'myCampaigns']);

    Route::get('/goals', [GoalController::class, 'index']);
    Route::get('/target-audiences', [TargetAudienceController::class, 'index']);
    Route::get('/tools', [ToolController::class, 'index']);
});