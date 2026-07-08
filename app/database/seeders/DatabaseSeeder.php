<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lafitmeal.com'],
            [
                'name' => 'LaFitMeal Admin',
                'password' => 'ChangeMe@123',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        Setting::set('skip_cutoff_hours', 12);
        Setting::set('currency', 'INR');
        Setting::set('currency_symbol', '₹');
        Setting::set('min_plan_days', 15);
        Setting::set('delivery_charge', 0);

        $this->call(MealSeeder::class);
    }
}
