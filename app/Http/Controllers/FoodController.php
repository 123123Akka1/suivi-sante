<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FoodController extends Controller
{
    public function scanFood(Request $request)
    {
        $query = $request->query('query');

        $response = Http::get('https://world.openfoodfacts.org/cgi/search.pl', [
            'search_terms' => $query,
            'search_simple' => 1,
            'action' => 'process',
            'json' => 1,
        ]);

        $data = $response->json();

        if (!empty($data['products'])) {
            $product = $data['products'][0];

            return response()->json([
                'name' => $product['product_name'] ?? $query,
                'calories' => $product['nutriments']['energy-kcal_100g'] ?? 0,
                'protein' => $product['nutriments']['proteins_100g'] ?? 0,
                'fat' => $product['nutriments']['fat_100g'] ?? 0,
                'carbs' => $product['nutriments']['carbohydrates_100g'] ?? 0,
            ]);
        }

        return response()->json(['error' => 'Food not found'], 404);
    }
}
