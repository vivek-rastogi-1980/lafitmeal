<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    protected $guarded = [];

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_ingredient')->withPivot(['quantity', 'sort_order']);
    }
}
