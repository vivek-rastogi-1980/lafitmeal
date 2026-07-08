<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isValidFor(float $amount): bool
    {
        return $this->is_active
            && $amount >= (float) $this->min_amount
            && (! $this->valid_from || now()->toDateString() >= $this->valid_from->toDateString())
            && (! $this->valid_until || now()->toDateString() <= $this->valid_until->toDateString())
            && (! $this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function discountFor(float $amount): float
    {
        return round($this->type === 'percent' ? $amount * ((float) $this->value / 100) : min((float) $this->value, $amount), 2);
    }
}
