<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'category',
        'calories_per_100g',
        'protein',
        'carbs',
        'fat',
        'unit',
    ];
}