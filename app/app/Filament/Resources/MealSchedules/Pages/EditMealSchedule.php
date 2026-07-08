<?php

namespace App\Filament\Resources\MealSchedules\Pages;

use App\Filament\Resources\MealSchedules\MealScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMealSchedule extends EditRecord
{
    protected static string $resource = MealScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
