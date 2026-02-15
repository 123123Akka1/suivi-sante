<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du aliment
            $table->string('name_ar')->nullable(); // Nom en arabe
            $table->string('category'); // Petit-déjeuner, Déjeuner, etc.
            $table->integer('calories_per_100g'); // Calories pour 100g
            $table->decimal('protein', 5, 2)->nullable(); // Protéines
            $table->decimal('carbs', 5, 2)->nullable(); // Glucides
            $table->decimal('fat', 5, 2)->nullable(); // Lipides
            $table->string('unit')->default('g'); // unité (g, ml, portion)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};