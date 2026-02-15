<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Meal;
use App\Models\Goal;
use Carbon\Carbon;

class RecommendationService
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function generateDailyRecommendations()
    {
        $recommendations = [];

        // 1. Activity Analysis
        $activityRec = $this->analyzeActivities();
        if ($activityRec) {
            $recommendations[] = $activityRec;
        }

        // 2. Calories Analysis
        $caloriesRec = $this->analyzeCalories();
        if ($caloriesRec) {
            $recommendations[] = $caloriesRec;
        }

        // 3. Goals Progress
        $goalsRec = $this->analyzeGoals();
        if ($goalsRec) {
            $recommendations[] = $goalsRec;
        }

        // 4. Weekly Motivation
        $motivationRec = $this->getMotivation();
        if ($motivationRec) {
            $recommendations[] = $motivationRec;
        }

        return $recommendations;
    }

    private function analyzeActivities()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Today's activities
        $todayActivities = Activity::where('user_id', $this->user->id)
            ->whereDate('date', $today)
            ->get();

        $todayDuration = $todayActivities->sum('duration');
        $todayCalories = $todayActivities->sum('calories');

        // Yesterday's activities
        $yesterdayActivities = Activity::where('user_id', $this->user->id)
            ->whereDate('date', $yesterday)
            ->get();

        $yesterdayDuration = $yesterdayActivities->sum('duration');

        // Analysis
        if ($todayDuration == 0) {
            return [
                'type' => 'activity',
                'priority' => 'high',
                'icon' => '🏃',
                'title' => 'Aucune activité aujourd\'hui',
                'message' => 'Bougez un peu ! Essayez une marche de 30 minutes.',
                'action' => 'add_activity',
            ];
        }

        if ($todayDuration < 30) {
            return [
                'type' => 'activity',
                'priority' => 'medium',
                'icon' => '💪',
                'title' => 'Bon début !',
                'message' => "Vous avez fait {$todayDuration} min d'activité. Essayez d'atteindre 30 min !",
                'action' => 'add_activity',
            ];
        }

        if ($todayDuration >= 30) {
            return [
                'type' => 'activity',
                'priority' => 'low',
                'icon' => '🎉',
                'title' => 'Excellent travail !',
                'message' => "Vous avez brûlé {$todayCalories} calories aujourd'hui !",
                'action' => null,
            ];
        }

        return null;
    }

    private function analyzeCalories()
    {
        $today = Carbon::today();

        $todayMeals = Meal::where('user_id', $this->user->id)
            ->whereDate('date', $today)
            ->get();

        $todayCalories = $todayMeals->sum('calories');

        // Recommended daily calories (simple calculation)
        $recommendedCalories = 2000; // Default, can be based on user profile

        if ($todayCalories == 0) {
            return [
                'type' => 'nutrition',
                'priority' => 'medium',
                'icon' => '🍽️',
                'title' => 'Aucun repas enregistré',
                'message' => 'N\'oubliez pas d\'enregistrer vos repas !',
                'action' => 'add_meal',
            ];
        }

        if ($todayCalories > $recommendedCalories + 500) {
            $excess = $todayCalories - $recommendedCalories;
            return [
                'type' => 'nutrition',
                'priority' => 'high',
                'icon' => '⚠️',
                'title' => 'Calories élevées',
                'message' => "Vous avez consommé {$todayCalories} cal. Essayez de réduire de {$excess} cal.",
                'action' => null,
            ];
        }

        if ($todayCalories < $recommendedCalories - 500) {
            $deficit = $recommendedCalories - $todayCalories;
            return [
                'type' => 'nutrition',
                'priority' => 'medium',
                'icon' => '🥗',
                'title' => 'Calories faibles',
                'message' => "Vous n'avez consommé que {$todayCalories} cal. Ajoutez {$deficit} cal.",
                'action' => 'add_meal',
            ];
        }

        return [
            'type' => 'nutrition',
            'priority' => 'low',
            'icon' => '✅',
            'title' => 'Équilibre parfait !',
            'message' => "Vous avez consommé {$todayCalories} calories aujourd'hui.",
            'action' => null,
        ];
    }

    private function analyzeGoals()
    {
        $activeGoals = Goal::where('user_id', $this->user->id)
            ->whereRaw('CAST(current_value AS DECIMAL) < CAST(target_value AS DECIMAL)')
            ->get();

        if ($activeGoals->isEmpty()) {
            return [
                'type' => 'goals',
                'priority' => 'low',
                'icon' => '🎯',
                'title' => 'Définissez des objectifs',
                'message' => 'Créez des objectifs pour rester motivé !',
                'action' => 'add_goal',
            ];
        }

        $nearCompletion = $activeGoals->filter(function ($goal) {
            $progress = ($goal->current_value / $goal->target_value) * 100;
            return $progress >= 80 && $progress < 100;
        })->first();

        if ($nearCompletion) {
            $remaining = $nearCompletion->target_value - $nearCompletion->current_value;
            return [
                'type' => 'goals',
                'priority' => 'high',
                'icon' => '🔥',
                'title' => 'Presque atteint !',
                'message' => "Plus que {$remaining} {$nearCompletion->unit} pour votre objectif !",
                'action' => 'view_goals',
            ];
        }

        return null;
    }

    private function getMotivation()
    {
        $messages = [
            [
                'type' => 'motivation',
                'priority' => 'low',
                'icon' => '💫',
                'title' => 'Citation du jour',
                'message' => 'Le succès est la somme de petits efforts répétés jour après jour.',
                'action' => null,
            ],
            [
                'type' => 'motivation',
                'priority' => 'low',
                'icon' => '🌟',
                'title' => 'Restez motivé',
                'message' => 'Chaque pas compte. Continuez comme ça !',
                'action' => null,
            ],
            [
                'type' => 'motivation',
                'priority' => 'low',
                'icon' => '💪',
                'title' => 'Vous êtes sur la bonne voie',
                'message' => 'La constance est la clé du succès !',
                'action' => null,
            ],
        ];

        return $messages[array_rand($messages)];
    }

    public function getWeeklySummary()
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $weekActivities = Activity::where('user_id', $this->user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();

        $weekMeals = Meal::where('user_id', $this->user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();

        $totalDuration = $weekActivities->sum('duration');
        $totalCaloriesBurned = $weekActivities->sum('calories');
        $totalCaloriesConsumed = $weekMeals->sum('calories');
        $activeDays = $weekActivities->pluck('date')->unique()->count();

        return [
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
            'total_duration' => $totalDuration,
            'total_calories_burned' => $totalCaloriesBurned,
            'total_calories_consumed' => $totalCaloriesConsumed,
            'active_days' => $activeDays,
            'avg_daily_duration' => $activeDays > 0 ? round($totalDuration / $activeDays) : 0,
        ];
    }
}