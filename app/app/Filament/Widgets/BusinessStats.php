<?php

namespace App\Filament\Widgets;

use App\Models\MealSchedule;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayMeals = MealSchedule::whereDate('date', today())
            ->whereNotIn('status', ['skipped'])
            ->count();

        $revenueMonth = Payment::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $activeSubs = Subscription::where('status', 'active')
            ->whereDate('end_date', '>=', today())
            ->count();

        $walletLiability = Wallet::sum('balance');

        return [
            Stat::make('Meals to cook today', $todayMeals)
                ->description(MealSchedule::whereDate('date', today())->where('status', 'skipped')->count() . ' skipped')
                ->icon('heroicon-o-fire')
                ->color('success'),
            Stat::make('Revenue this month', '₹' . number_format((float) $revenueMonth, 2))
                ->description(Payment::where('status', 'paid')->whereDate('created_at', today())->count() . ' payments today')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Active subscribers', $activeSubs)
                ->description(User::where('role', 'customer')->count() . ' total customers')
                ->icon('heroicon-o-user-group'),
            Stat::make('Wallet liability', '₹' . number_format((float) $walletLiability, 2))
                ->description('credits owed to customers')
                ->icon('heroicon-o-wallet')
                ->color('warning'),
        ];
    }
}
