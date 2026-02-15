<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ActivityControllers;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\RecommendationController ;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\FoodController;

Route::get('/food-scan', [FoodController::class, 'scanFood']);

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/test', function() {
    return response()->json(['message' => 'API working']);
});

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    
    // Stats
    Route::get('/stats', [StatsController::class, 'getStats']);
    
    // Activities
    Route::get('/activities', [ActivityControllers::class, 'index']);
    Route::post('/activities', [ActivityControllers::class, 'store']);
    Route::put('/activities/{id}/toggle', [ActivityControllers::class, 'toggleCompleted']);
    Route::delete('/activities/{id}', [ActivityControllers::class, 'destroy']);
    
    // Meals
    Route::get('/meals', [MealController::class, 'index']);
    Route::post('/meals', [MealController::class, 'store']);
    Route::delete('/meals/{id}', [MealController::class, 'destroy']);
    
    // Goals
    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{id}', [GoalController::class, 'update']);  // ← Ajoute cette ligne
    Route::delete('/goals/{id}', [GoalController::class, 'destroy']);


    Route::get('/statistics', [StatsController::class, 'getStatistics']);
    Route::get('/statistics/daily', [StatsController::class, 'getDailyStats']);
    Route::get('/statistics/weekly', [StatsController::class, 'getWeeklyStats']);
    Route::get('/statistics/monthly', [StatsController::class, 'getMonthlyStats']);


    // Recommendations
    Route::get('/recommendations', [RecommendationController::class, 'getDailyRecommendations']);
    Route::get('/recommendations/weekly', [RecommendationController::class, 'getWeeklySummary']);
    Route::get('/food-items/search', [FoodItemController::class, 'search']);
});
