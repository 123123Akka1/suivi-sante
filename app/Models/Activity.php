<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
    'user_id',
    'type',
    'duration',
    'distance',
    'calories',
    'date',
    'completed', // ← زيد هنا
];

protected $casts = [
    'duration' => 'integer',
    'distance' => 'float',
    'calories' => 'integer',
    'completed' => 'boolean', // ← زيد هنا
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
