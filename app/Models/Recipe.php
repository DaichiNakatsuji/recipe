<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'instructions', 'is_public', 'publish_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient', 'recipe_id', 'ingredient_id')
                    ->withPivot('num')
                    ->withTimestamps();
    }
}