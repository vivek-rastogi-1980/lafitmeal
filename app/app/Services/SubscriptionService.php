<?php

namespace App\Services;

use App\Models\MealSchedule;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Payments\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        protected PricingService $pricing,
        protected PaymentGateway $gateway,
    ) {}

    /**
     * Create a pending subscription from the onboarding wizard payload,
     * apply wallet balance, and initiate payment for the remainder.
     */
    public function create(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {
            $quote = $this->pricing->quote(
                $data['slots'],
                $data['start_date'],
                (int) $data['duration_days'],
                $data['coupon_code'] ?? null
            );

            $wallet = $user->getOrCreateWallet();
            $walletApplied = min((float) $wallet->balance, $quote['total']);
            $payable = round($quote['total'] - $walletApplied, 2);

            $subscription = $user->subscriptions()->create([
                'address_id' => $data['address_id'],
                'meal_category' => $data['meal_category'],
                'start_date' => $data['start_date'],
                'end_date' => Carbon::parse($data['start_date'])->addDays((int) $data['duration_days'] - 1),
                'duration_days' => $data['duration_days'],
                'status' => 'pending_payment',
                'subtotal' => $quote['subtotal'],
                'discount' => $quote['discount'],
                'wallet_applied' => $walletApplied,
                'total_paid' => 0,
                'coupon_code' => $data['coupon_code'] ?? null,
            ]);

            foreach ($data['slots'] as $slot) {
                $subscription->slots()->create([
                    'day_of_week' => $slot['day_of_week'],
                    'meal_time' => $slot['meal_time'],
                    'meal_id' => $slot['meal_id'] ?? null,
                    'enabled' => (bool) ($slot['enabled'] ?? false),
                ]);
            }

            $payment = $user->payments()->create([
                'subscription_id' => $subscription->id,
                'gateway' => config('services.payment.driver', 'offline'),
                'amount' => $payable,
                'currency' => Setting::get('currency', 'INR'),
                'status' => 'pending',
            ]);

            $gatewayData = $this->gateway->initiate($payment);

            return compact('subscription', 'payment', 'gatewayData');
        });
    }

    /**
     * Called after gateway confirms payment: debit wallet portion,
     * activate the subscription and generate the meal calendar.
     */
    public function activate(Subscription $subscription, Payment $payment): void
    {
        DB::transaction(function () use ($subscription, $payment) {
            if ((float) $subscription->wallet_applied > 0) {
                $subscription->user->getOrCreateWallet()->debit(
                    (float) $subscription->wallet_applied,
                    "Applied to subscription #{$subscription->id}",
                    $subscription
                );
            }

            $subscription->update([
                'status' => 'active',
                'total_paid' => $payment->amount,
            ]);

            $this->generateSchedules($subscription);
        });
    }

    /**
     * Materialise the weekly template into dated meal_schedules rows.
     */
    public function generateSchedules(Subscription $subscription): void
    {
        $slots = $subscription->slots()->where('enabled', true)->whereNotNull('meal_id')->with('meal')->get()
            ->keyBy(fn ($s) => $s->day_of_week . ':' . $s->meal_time);

        $start = $subscription->start_date->copy();

        for ($i = 0; $i < $subscription->duration_days; $i++) {
            $date = $start->copy()->addDays($i);
            foreach (['breakfast', 'lunch', 'dinner'] as $mealTime) {
                $slot = $slots->get($date->dayOfWeek . ':' . $mealTime);
                if (! $slot) {
                    continue;
                }
                MealSchedule::updateOrCreate(
                    [
                        'subscription_id' => $subscription->id,
                        'date' => $date->toDateString(),
                        'meal_time' => $mealTime,
                    ],
                    [
                        'user_id' => $subscription->user_id,
                        'meal_id' => $slot->meal_id,
                        'unit_price' => $slot->meal->price,
                        'status' => 'scheduled',
                    ]
                );
            }
        }
    }

    /**
     * Skip a single scheduled meal; credit its value to the wallet.
     */
    public function skipMeal(MealSchedule $schedule): void
    {
        if (! $schedule->isSkippable()) {
            throw new \RuntimeException('This meal can no longer be skipped — the cutoff time has passed.');
        }

        DB::transaction(function () use ($schedule) {
            $schedule->update(['status' => 'skipped', 'skipped_at' => now()]);

            $schedule->user->getOrCreateWallet()->credit(
                (float) $schedule->unit_price,
                "Skipped {$schedule->meal_time} on {$schedule->date->format('d M Y')}",
                $schedule
            );
        });
    }

    /**
     * Un-skip (re-enable) a skipped meal if still before cutoff; reverses the credit.
     */
    public function unskipMeal(MealSchedule $schedule): void
    {
        if ($schedule->status !== 'skipped') {
            return;
        }

        $cutoffHours = (int) Setting::get('skip_cutoff_hours', 12);
        if (now()->gte($schedule->date->copy()->startOfDay()->subHours($cutoffHours))) {
            throw new \RuntimeException('The cutoff time has passed; this meal cannot be re-enabled.');
        }

        DB::transaction(function () use ($schedule) {
            $schedule->update(['status' => 'scheduled', 'skipped_at' => null]);

            $schedule->user->getOrCreateWallet()->debit(
                (float) $schedule->unit_price,
                "Re-enabled {$schedule->meal_time} on {$schedule->date->format('d M Y')}",
                $schedule
            );
        });
    }

    /**
     * Skip every remaining skippable meal on a given date.
     */
    public function skipDay(Subscription $subscription, string $date): int
    {
        $count = 0;
        $schedules = $subscription->mealSchedules()->whereDate('date', $date)->where('status', 'scheduled')->get();
        foreach ($schedules as $schedule) {
            if ($schedule->isSkippable()) {
                $this->skipMeal($schedule);
                $count++;
            }
        }

        return $count;
    }
}
