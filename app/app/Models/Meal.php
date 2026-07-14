<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    public const CATEGORIES = ['veg', 'non_veg', 'vegan'];
    public const MEAL_TIMES = ['breakfast', 'lunch', 'dinner'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'allergens' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'meal_ingredient')
            ->withPivot(['quantity', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function cookingSteps(): HasMany
    {
        return $this->hasMany(CookingStep::class)->orderBy('step_number');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'veg' => 'Veg',
            'non_veg' => 'Non-Veg',
            'vegan' => 'Vegan',
            default => $this->category,
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->hasImage() ? asset('storage/' . $this->image_path) : null;
    }

    public function hasImage(): bool
    {
        return $this->image_path && file_exists(public_path('storage/' . $this->image_path));
    }

    /** Path (relative to storage) of the looping cinemagraph, if one has been generated. */
    public function getVideoPathAttribute(): string
    {
        return 'meals/videos/' . $this->slug . '.mp4';
    }

    /** Public URL of the animated cinemagraph, or null when only a still image exists. */
    public function getVideoUrlAttribute(): ?string
    {
        return $this->hasVideo() ? asset('storage/' . $this->video_path) : null;
    }

    public function hasVideo(): bool
    {
        return $this->slug && file_exists(public_path('storage/' . $this->video_path));
    }

    /** Emoji used by the animated placeholder tile until real images exist. */
    public function getEmojiAttribute(): string
    {
        return match ($this->meal_time) {
            'breakfast' => match ($this->category) { 'non_veg' => '🍳', 'vegan' => '🥑', default => '🥣' },
            'lunch' => match ($this->category) { 'non_veg' => '🍗', 'vegan' => '🥗', default => '🍛' },
            default => match ($this->category) { 'non_veg' => '🐟', 'vegan' => '🍜', default => '🥘' },
        };
    }
}
