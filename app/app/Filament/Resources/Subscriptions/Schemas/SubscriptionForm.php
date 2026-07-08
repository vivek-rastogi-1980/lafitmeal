<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('address_id')
                    ->relationship('address', 'id'),
                TextInput::make('meal_category')
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                TextInput::make('duration_days')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending_payment'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('wallet_applied')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_paid')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('coupon_code'),
                DatePicker::make('paused_from'),
                DatePicker::make('paused_until'),
            ]);
    }
}
