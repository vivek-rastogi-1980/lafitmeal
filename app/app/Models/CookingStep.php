<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CookingStep extends Model
{
    protected $guarded = [];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
