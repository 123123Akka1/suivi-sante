<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function getDailyRecommendations(Request $request)
    {
        $user = $request->user();
        $service = new RecommendationService($user);
        
        $recommendations = $service->generateDailyRecommendations();
        
        return response()->json([
            'recommendations' => $recommendations,
        ]);
    }

    public function getWeeklySummary(Request $request)
    {
        $user = $request->user();
        $service = new RecommendationService($user);
        
        $summary = $service->getWeeklySummary();
        
        return response()->json($summary);
    }
}