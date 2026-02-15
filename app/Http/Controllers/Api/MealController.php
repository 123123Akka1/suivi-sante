<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MealController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->meals;
    }

    public function store(Request $request)
    {
        $meal = Meal::create([
            'user_id' => $request->user()->id,
            'meal_name' => $request->meal_name,
            'calories' => $request->calories,
            'meal_time' => $request->meal_time,
            'date' => $request->date,
        ]);

        return response()->json($meal);
    }

    public function destroy($id)
    {
        Meal::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
