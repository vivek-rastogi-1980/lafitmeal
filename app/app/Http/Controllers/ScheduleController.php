<?php

namespace App\Http\Controllers;

use App\Models\MealSchedule;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(protected SubscriptionService $service) {}

    public function skip(Request $request, MealSchedule $schedule)
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

        try {
            $this->service->skipMeal($schedule);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['schedule' => $e->getMessage()]);
        }

        return back()->with('status', 'meal-skipped');
    }

    public function unskip(Request $request, MealSchedule $schedule)
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

        try {
            $this->service->unskipMeal($schedule);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['schedule' => $e->getMessage()]);
        }

        return back()->with('status', 'meal-restored');
    }

    public function skipDay(Request $request)
    {
        $data = $request->validate(['date' => ['required', 'date', 'after_or_equal:today']]);

        $subscription = $request->user()->activeSubscription;
        abort_unless($subscription, 404);

        $count = $this->service->skipDay($subscription, $data['date']);

        return back()->with('status', $count > 0 ? 'day-skipped' : 'nothing-to-skip');
    }
}
