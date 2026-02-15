<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        $foods = FoodItem::where('name', 'LIKE', "%$query%")
                        ->orWhere('name_ar', 'LIKE', "%$query%")
                        ->limit(20)
                        ->get();
        
        return response()->json($foods);
    }
    
    public function categories()
    {
        $categories = FoodItem::select('category')
                             ->distinct()
                             ->get();
        
        return response()->json($categories);
    }
}