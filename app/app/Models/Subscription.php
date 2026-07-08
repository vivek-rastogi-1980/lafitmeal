<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    public const MIN_DURATION_DAYS = 15;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'paused_from' => 'date',
            'paused_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'wallet_applied' => 'decimal:2',
            'total_paid' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(SubscriptionSlot::class);
    }

    public function mealSchedules(): HasMany
    {
        return $this->hasMany(MealSchedule::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->toDateString() <= $this->end_date->toDateString();
    }
}
