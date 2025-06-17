<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['ingredient_name'];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredient', 'ingredient_id', 'recipe_id')
            ->withPivot('num')
            ->withTimestamps();
    }
}