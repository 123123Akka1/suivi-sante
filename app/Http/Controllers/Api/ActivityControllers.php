<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Log;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityControllers extends Controller
{


    public function store(Request $request)
    {
        try {
            Log::info('=== START ADD ACTIVITY ===');  // ← دابا بلا \
            Log::info('Request data', ['data' => $request->all()]);
            
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'duration' => 'required|integer|min:1',
                'distance' => 'required|numeric|min:0',
                'calories' => 'required|integer|min:0',
                'date' => 'nullable|date',
            ]);

            $activity = Activity::create([
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'duration' => $validated['duration'],
                'distance' => $validated['distance'],
                'calories' => $validated['calories'],
                'date' => $validated['date'] ?? now()->toDateString(),
            ]);

            Log::info('Activity created', ['activity' => $activity->toArray()]);

            return response()->json($activity, 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error', ['errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
            
        } catch (\Exception $e) {
            Log::error('Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function toggleCompleted(Request $request, $id)
{
    try {
        $activity = Activity::where('id', $id)
                           ->where('user_id', $request->user()->id)
                           ->firstOrFail();
        
        $activity->completed = !$activity->completed;
        $activity->save();
        
        Log::info('Activity toggled', [
            'activity_id' => $activity->id,
            'completed' => $activity->completed
        ]);
        
        return response()->json($activity);
    } catch (\Exception $e) {
        Log::error('Toggle activity error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function index(Request $request)
    {
        try {
            Log::info('Getting activities for user', ['user_id' => $request->user()->id]);
            
            $activities = Activity::where('user_id', $request->user()->id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();
            
            Log::info('Activities found', ['count' => $activities->count()]);
            
            return response()->json($activities);
            
        } catch (\Exception $e) {
            Log::error('Error getting activities', [
                'message' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'unknown'
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Activity::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
    

}
