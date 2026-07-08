<?php

namespace App\Filament\Widgets;

use App\Models\MealSchedule;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * What the kitchen must cook today: today's scheduled meals
 * aggregated by dish, with portion counts.
 */
class KitchenSheet extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = "Today's kitchen sheet";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MealSchedule::query()
                    ->whereDate('date', today())
                    ->whereNotIn('status', ['skipped'])
                    ->selectRaw('MIN(meal_schedules.id) as id, meal_id, meal_time, COUNT(*) as portions')
                    ->groupBy('meal_id', 'meal_time')
            )
            ->columns([
                TextColumn::make('meal_time')
                    ->label('Meal time')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'breakfast' => 'warning',
                        'lunch' => 'success',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('meal.name')->label('Dish'),
                TextColumn::make('meal.category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str_replace('_', '-', ucfirst($state))),
                TextColumn::make('portions')->label('Portions')->weight('bold'),
            ])
            ->defaultSort('meal_time')
            ->paginated(false);
    }
}
