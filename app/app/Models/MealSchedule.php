<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealSchedule extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'unit_price' => 'decimal:2',
            'skipped_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    /**
     * A meal can be skipped only while it is still 'scheduled' and the
     * cutoff time (settings: skip_cutoff_hours before the delivery day)
     * has not passed.
     */
    public function isSkippable(): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }

        $cutoffHours = (int) Setting::get('skip_cutoff_hours', 12);

        return now()->lt($this->date->copy()->startOfDay()->subHours($cutoffHours));
    }
}
