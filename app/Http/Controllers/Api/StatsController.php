<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Activity;
use App\Models\Goal;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class StatsController extends Controller
{
   public function getStats(Request $request)
{
    try {
        $user = $request->user();
        
        Log::info('Getting stats for user', ['user_id' => $user->id]);
        
        // جيب كل الـ goals
        $goals = Goal::where('user_id', $user->id)->get();
        
        // حسب الـ completed goals (اللي current_value >= target_value)
        $completedGoals = $goals->filter(function($goal) {
            return (float)$goal->current_value >= (float)$goal->target_value;
        })->count();
        
        $stats = [
            'total_meals' => Meal::where('user_id', $user->id)->count(),
            'total_activities' => Activity::where('user_id', $user->id)->count(),
            'completed_activities' => Activity::where('user_id', $user->id)
                                            ->where('completed', true)
                                            ->count(),
            'total_goals' => $goals->count(),
            'completed_goals' => $completedGoals, // ← استعمل الحساب الجديد
        ];
        
        Log::info('Stats found', $stats);
        
        return response()->json($stats);
        
    } catch (\Exception $e) {
        Log::error('Error getting stats: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function getStatistics(Request $request)
    {
        $user = $request->user();
        
        // Statistiques aujourd'hui
        $today = Carbon::today();
        
        $todayActivities = Activity::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get();
            
        $todayMeals = Meal::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get();
        
        // Statistiques cette semaine
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $weekActivities = Activity::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();
            
        $weekMeals = Meal::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();
        
        // Objectifs
        $goals = Goal::where('user_id', $user->id)->get();
        $completedGoals = $goals->filter(function($goal) {
            return (float)$goal->current_value >= (float)$goal->target_value;
        })->count();
        
        // Calcul IMC si les données existent
        $bmi = null;
        if ($user->weight && $user->height) {
            $heightInMeters = $user->height / 100;
            $bmi = round($user->weight / ($heightInMeters * $heightInMeters), 1);
        }
        
        return response()->json([
            'user' => [
                'name' => $user->name,
                'weight' => $user->weight,
                'height' => $user->height,
                'age' => $user->age,
                'bmi' => $bmi,
            ],
            'today' => [
                'activities_count' => $todayActivities->count(),
                'total_calories_burned' => $todayActivities->sum('calories'),
                'total_duration' => $todayActivities->sum('duration'),
                'total_distance' => $todayActivities->sum('distance'),
                'meals_count' => $todayMeals->count(),
                'total_calories_consumed' => $todayMeals->sum('calories'),
                'net_calories' => $todayMeals->sum('calories') - $todayActivities->sum('calories'),
            ],
            'this_week' => [
                'activities_count' => $weekActivities->count(),
                'total_calories_burned' => $weekActivities->sum('calories'),
                'total_duration' => $weekActivities->sum('duration'),
                'total_distance' => $weekActivities->sum('distance'),
                'meals_count' => $weekMeals->count(),
                'total_calories_consumed' => $weekMeals->sum('calories'),
            ],
            'goals' => [
                'total' => $goals->count(),
                'completed' => $completedGoals,
                'in_progress' => $goals->count() - $completedGoals,
            ],
        ]);
    }

    /**
     * Statistiques journalières (7 derniers jours)
     */
    public function getDailyStats(Request $request)
    {
        $user = $request->user();
        $days = 7;
        
        $stats = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            $activities = Activity::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->get();
                
            $meals = Meal::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->get();
            
            $stats[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('fr')->isoFormat('ddd'), // Lun, Mar, Mer...
                'activities' => [
                    'count' => $activities->count(),
                    'calories' => $activities->sum('calories'),
                    'duration' => $activities->sum('duration'),
                    'distance' => $activities->sum('distance'),
                ],
                'meals' => [
                    'count' => $meals->count(),
                    'calories' => $meals->sum('calories'),
                ],
                'net_calories' => $meals->sum('calories') - $activities->sum('calories'),
            ];
        }
        
        return response()->json($stats);
    }

    /**
     * Statistiques hebdomadaires (4 dernières semaines)
     */
    public function getWeeklyStats(Request $request)
    {
        $user = $request->user();
        $weeks = 4;
        
        $stats = [];
        
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $activities = Activity::where('user_id', $user->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->get();
                
            $meals = Meal::where('user_id', $user->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->get();
            
            $stats[] = [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'week_label' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'),
                'activities' => [
                    'count' => $activities->count(),
                    'calories' => $activities->sum('calories'),
                    'duration' => $activities->sum('duration'),
                    'distance' => $activities->sum('distance'),
                ],
                'meals' => [
                    'count' => $meals->count(),
                    'calories' => $meals->sum('calories'),
                ],
                'net_calories' => $meals->sum('calories') - $activities->sum('calories'),
                'average_daily_calories_burned' => $activities->count() > 0 ? round($activities->sum('calories') / 7, 0) : 0,
                'average_daily_calories_consumed' => $meals->count() > 0 ? round($meals->sum('calories') / 7, 0) : 0,
            ];
        }
        
        return response()->json($stats);
    }

    /**
     * Statistiques mensuelles (6 derniers mois)
     */
    public function getMonthlyStats(Request $request)
    {
        $user = $request->user();
        $months = 6;
        
        $stats = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $activities = Activity::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->get();
                
            $meals = Meal::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->get();
            
            // Grouper activités par type
            $activitiesByType = $activities->groupBy('type');
            $typeStats = [];
            foreach ($activitiesByType as $type => $typeActivities) {
                $typeStats[$type] = [
                    'count' => $typeActivities->count(),
                    'calories' => $typeActivities->sum('calories'),
                    'duration' => $typeActivities->sum('duration'),
                    'distance' => $typeActivities->sum('distance'),
                ];
            }
            
            $daysInMonth = $monthStart->daysInMonth;
            
            $stats[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->locale('fr')->isoFormat('MMMM YYYY'), // Janvier 2026
                'activities' => [
                    'count' => $activities->count(),
                    'calories' => $activities->sum('calories'),
                    'duration' => $activities->sum('duration'),
                    'distance' => $activities->sum('distance'),
                    'by_type' => $typeStats,
                ],
                'meals' => [
                    'count' => $meals->count(),
                    'calories' => $meals->sum('calories'),
                ],
                'net_calories' => $meals->sum('calories') - $activities->sum('calories'),
                'average_daily_calories_burned' => $activities->count() > 0 ? round($activities->sum('calories') / $daysInMonth, 0) : 0,
                'average_daily_calories_consumed' => $meals->count() > 0 ? round($meals->sum('calories') / $daysInMonth, 0) : 0,
            ];
        }
        
        return response()->json($stats);
    }
}