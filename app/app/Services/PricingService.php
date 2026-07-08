<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Meal;
use Carbon\Carbon;

class PricingService
{
    /**
     * Price a plan from a weekly template.
     *
     * @param array $slots [['day_of_week' => 0-6, 'meal_time' => 'breakfast', 'meal_id' => 1, 'enabled' => true], ...]
     * @return array{subtotal: float, discount: float, total: float, meal_count: int, breakdown: array}
     */
    public function quote(array $slots, string $startDate, int $durationDays, ?string $couponCode = null): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $mealPrices = Meal::whereIn('id', collect($slots)->pluck('meal_id')->filter())->pluck('price', 'id');

        // Index enabled slots by "dayOfWeek:mealTime"
        $template = [];
        foreach ($slots as $slot) {
            if (! empty($slot['enabled']) && ! empty($slot['meal_id'])) {
                $template[$slot['day_of_week'] . ':' . $slot['meal_time']] = (float) $mealPrices[$slot['meal_id']];
            }
        }

        $subtotal = 0.0;
        $mealCount = 0;
        $breakdown = [];

        for ($i = 0; $i < $durationDays; $i++) {
            $date = $start->copy()->addDays($i);
            $dayTotal = 0.0;
            foreach (['breakfast', 'lunch', 'dinner'] as $mealTime) {
                $key = $date->dayOfWeek . ':' . $mealTime;
                if (isset($template[$key])) {
                    $dayTotal += $template[$key];
                    $mealCount++;
                }
            }
            if ($dayTotal > 0) {
                $breakdown[$date->toDateString()] = $dayTotal;
            }
            $subtotal += $dayTotal;
        }

        $discount = 0.0;
        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper($couponCode))->first();
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $discount = $coupon->discountFor($subtotal);
            }
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => $discount,
            'total' => round($subtotal - $discount, 2),
            'meal_count' => $mealCount,
            'breakdown' => $breakdown,
        ];
    }
}
