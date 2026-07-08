<?php

namespace App\Filament\Resources\MealSchedules\Pages;

use App\Filament\Resources\MealSchedules\MealScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMealSchedules extends ListRecords
{
    protected static string $resource = MealScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
