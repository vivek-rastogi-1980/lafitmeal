<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $subscription = $user->activeSubscription()->with('address')->first()
            ?? $user->subscriptions()->latest()->first();

        $schedules = collect();
        if ($subscription && $subscription->status === 'active') {
            $schedules = $subscription->mealSchedules()
                ->with('meal:id,name,slug,category,meal_time,image_path,calories,protein_g')
                ->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->get()
                ->groupBy(fn ($s) => $s->date->toDateString());
        }

        return view('dashboard', [
            'subscription' => $subscription,
            'schedulesByDate' => $schedules,
            'wallet' => $user->getOrCreateWallet(),
            'today' => $subscription?->status === 'active'
                ? $subscription->mealSchedules()->with('meal')->whereDate('date', now())->get()
                : collect(),
        ]);
    }
}
