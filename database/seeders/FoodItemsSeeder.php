<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodItem;

class FoodItemsSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            // Petit-déjeuner
            ['name' => 'Pain blanc', 'name_ar' => 'الخبز الأبيض', 'category' => 'Petit-déjeuner', 'calories_per_100g' => 265, 'protein' => 9, 'carbs' => 49, 'fat' => 3.2],
            ['name' => 'Croissant', 'name_ar' => 'كرواسون', 'category' => 'Petit-déjeuner', 'calories_per_100g' => 406, 'protein' => 8, 'carbs' => 45, 'fat' => 21],
            ['name' => 'Œuf', 'name_ar' => 'بيض', 'category' => 'Petit-déjeuner', 'calories_per_100g' => 155, 'protein' => 13, 'carbs' => 1.1, 'fat' => 11, 'unit' => 'pièce'],
            ['name' => 'Lait', 'name_ar' => 'حليب', 'category' => 'Petit-déjeuner', 'calories_per_100g' => 61, 'protein' => 3.4, 'carbs' => 4.8, 'fat' => 3.3, 'unit' => 'ml'],
            ['name' => 'Café noir', 'name_ar' => 'قهوة', 'category' => 'Boisson', 'calories_per_100g' => 2, 'protein' => 0.1, 'carbs' => 0, 'fat' => 0, 'unit' => 'ml'],
            
            // Déjeuner/Dîner
            ['name' => 'Riz blanc', 'name_ar' => 'أرز أبيض', 'category' => 'Déjeuner', 'calories_per_100g' => 130, 'protein' => 2.7, 'carbs' => 28, 'fat' => 0.3],
            ['name' => 'Couscous', 'name_ar' => 'كسكس', 'category' => 'Déjeuner', 'calories_per_100g' => 112, 'protein' => 3.8, 'carbs' => 23, 'fat' => 0.2],
            ['name' => 'Poulet grillé', 'name_ar' => 'دجاج مشوي', 'category' => 'Viande', 'calories_per_100g' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6],
            ['name' => 'Poisson', 'name_ar' => 'سمك', 'category' => 'Viande', 'calories_per_100g' => 206, 'protein' => 22, 'carbs' => 0, 'fat' => 13],
            ['name' => 'Salade verte', 'name_ar' => 'سلطة خضراء', 'category' => 'Légume', 'calories_per_100g' => 15, 'protein' => 1.4, 'carbs' => 2.9, 'fat' => 0.2],
            ['name' => 'Tomate', 'name_ar' => 'طماطم', 'category' => 'Légume', 'calories_per_100g' => 18, 'protein' => 0.9, 'carbs' => 3.9, 'fat' => 0.2],
            ['name' => 'Huile d\'olive', 'name_ar' => 'زيت الزيتون', 'category' => 'Matière grasse', 'calories_per_100g' => 884, 'protein' => 0, 'carbs' => 0, 'fat' => 100, 'unit' => 'ml'],
            
            // Fruits
            ['name' => 'Pomme', 'name_ar' => 'تفاح', 'category' => 'Fruit', 'calories_per_100g' => 52, 'protein' => 0.3, 'carbs' => 14, 'fat' => 0.2],
            ['name' => 'Banane', 'name_ar' => 'موز', 'category' => 'Fruit', 'calories_per_100g' => 89, 'protein' => 1.1, 'carbs' => 23, 'fat' => 0.3],
            ['name' => 'Orange', 'name_ar' => 'برتقال', 'category' => 'Fruit', 'calories_per_100g' => 47, 'protein' => 0.9, 'carbs' => 12, 'fat' => 0.1],
            
            // Snacks
            ['name' => 'Chocolat noir', 'name_ar' => 'شوكولاتة سوداء', 'category' => 'Snack', 'calories_per_100g' => 546, 'protein' => 4.9, 'carbs' => 61, 'fat' => 31],
            ['name' => 'Chips', 'name_ar' => 'شيبس', 'category' => 'Snack', 'calories_per_100g' => 536, 'protein' => 6.6, 'carbs' => 53, 'fat' => 34],
            ['name' => 'Yaourt nature', 'name_ar' => 'ياغورت', 'category' => 'Produit laitier', 'calories_per_100g' => 59, 'protein' => 10, 'carbs' => 3.6, 'fat' => 0.4],
        ];

        foreach ($foods as $food) {
            FoodItem::create($food);
        }
    }
}