<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\ProfileApiController;
use App\Http\Controllers\API\TargetAudienceController;
use App\Http\Controllers\Api\ToolParameterController;
use App\Http\Controllers\API\ToolsController;
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
    Route::post('/microsoft', [AuthController::class, 'microsoftLogin']);
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
        Route::get('/full-profile', [ProfileApiController::class, 'show_full_profile']);
        Route::post('/profile', [ProfileApiController::class, 'update']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::post('/campaigns/update/{id}', [CampaignController::class, 'store']); // update);

    Route::get('/my-campaigns', [CampaignController::class, 'myCampaigns']);

    Route::get('/goals', [GoalController::class, 'index']);
    Route::get('/tools', [ToolsController::class, 'index']);
    Route::post('/assign-user-tools/{id}', [ToolsController::class, 'assignUserTools']);
    Route::get('/get-user-tools', [ToolsController::class, 'getUserTools']);
    // Route::get('/get-target-audiences', [ToolsController::class, 'getTargetAudience']);
    Route::get('/target-audiences', [TargetAudienceController::class, 'index']);
    Route::post('/target-audiences', [TargetAudienceController::class, 'store']);
    Route::Patch('/target-audiences/{id}', [TargetAudienceController::class, 'store']);
    Route::delete('/target-audiences/{id}', [TargetAudienceController::class, 'destroy']);
});
