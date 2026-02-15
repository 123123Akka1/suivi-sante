<?php

namespace App\Http\Controllers\Api;

use App\Models\Goal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->goals;
    }

    public function store(Request $request)
    {
        $goal = Goal::create([
            'user_id' => $request->user()->id,
            'goal_type' => $request->goal_type,
            'target_value' => $request->target_value,
            'current_value' => 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json($goal);
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'current_value' => 'required|numeric|min:0',
    ]);

    $goal = Goal::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    $goal->current_value = $request->current_value;
    $goal->save();

    return response()->json([
        'message' => 'Goal updated successfully',
        'goal' => $goal
    ]);
}

    public function destroy($id)
    {
        Goal::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
