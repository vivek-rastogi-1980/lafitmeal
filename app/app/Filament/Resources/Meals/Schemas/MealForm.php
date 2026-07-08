<?php

namespace App\Filament\Resources\Meals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('meal_time')
                    ->required(),
                TextInput::make('tagline'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('calories')
                    ->required()
                    ->numeric(),
                TextInput::make('protein_g')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('carbs_g')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('fat_g')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('fiber_g')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('image_path')
                    ->image(),
                TextInput::make('badge'),
                TextInput::make('prep_time_minutes')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('cook_time_minutes')
                    ->required()
                    ->numeric()
                    ->default(15),
                TextInput::make('spice_level')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('allergens')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
