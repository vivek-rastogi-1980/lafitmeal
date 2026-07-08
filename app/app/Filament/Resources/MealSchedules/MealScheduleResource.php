<?php

namespace App\Filament\Resources\MealSchedules;

use App\Filament\Resources\MealSchedules\Pages\CreateMealSchedule;
use App\Filament\Resources\MealSchedules\Pages\EditMealSchedule;
use App\Filament\Resources\MealSchedules\Pages\ListMealSchedules;
use App\Filament\Resources\MealSchedules\Schemas\MealScheduleForm;
use App\Filament\Resources\MealSchedules\Tables\MealSchedulesTable;
use App\Models\MealSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MealScheduleResource extends Resource
{
    protected static ?string $model = MealSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MealScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealSchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMealSchedules::route('/'),
            'create' => CreateMealSchedule::route('/create'),
            'edit' => EditMealSchedule::route('/{record}/edit'),
        ];
    }
}
