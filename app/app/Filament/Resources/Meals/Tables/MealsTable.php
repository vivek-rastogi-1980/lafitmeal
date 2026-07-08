<?php

namespace App\Filament\Resources\Meals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('meal_time')
                    ->searchable(),
                TextColumn::make('tagline')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('calories')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('protein_g')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('carbs_g')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fat_g')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fiber_g')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image_path'),
                TextColumn::make('badge')
                    ->searchable(),
                TextColumn::make('prep_time_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cook_time_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('spice_level')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
