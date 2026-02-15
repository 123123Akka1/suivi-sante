<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * Les champs autorisés pour l’insertion (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'weight',
        'height',
        'gender',
        'image', 
    ];

    /**
     * Les champs cachés dans les réponses JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversion des types
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'weight' => 'float',
            'height' => 'float',
            'age' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // User → Activities
    public function activities()
{
    return $this->hasMany(Activity::class);
}


    // User → Meals
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    // User → Goals
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}
