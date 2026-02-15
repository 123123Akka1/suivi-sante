<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [
        'user_id',
        'meal_name',
        'calories',
        'meal_time',
        'date',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
