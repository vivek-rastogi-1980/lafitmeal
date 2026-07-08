<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MealSeeder extends Seeder
{
    public function run(): void
    {
        $files = [
            'veg' => ['breakfast', 'lunch', 'dinner'],
            'non_veg' => ['breakfast', 'lunch', 'dinner'],
            'vegan' => ['breakfast', 'lunch', 'dinner'],
        ];

        $sort = 0;

        foreach ($files as $category => $mealTimes) {
            foreach ($mealTimes as $mealTime) {
                $path = database_path("seeders/data/{$category}_{$mealTime}.php");
                $meals = require $path;

                foreach ($meals as $i => $data) {
                    $meal = Meal::updateOrCreate(
                        ['slug' => Str::slug($data['name'])],
                        [
                            'name' => $data['name'],
                            'category' => $category,
                            'meal_time' => $mealTime,
                            'tagline' => $data['tagline'],
                            'description' => $data['description'],
                            'price' => $data['price'],
                            'calories' => $data['calories'],
                            'protein_g' => $data['protein_g'],
                            'carbs_g' => $data['carbs_g'],
                            'fat_g' => $data['fat_g'],
                            'fiber_g' => $data['fiber_g'],
                            'badge' => $data['badge'] ?? null,
                            'prep_time_minutes' => $data['prep'] ?? 10,
                            'cook_time_minutes' => $data['cook'] ?? 15,
                            'spice_level' => $data['spice_level'] ?? 1,
                            'allergens' => $data['allergens'] ?? [],
                            'image_path' => 'meals/' . Str::slug($data['name']) . '.webp',
                            'is_active' => true,
                            'is_featured' => $i < 2, // first two of each group featured on homepage
                            'sort_order' => $sort++,
                        ]
                    );

                    $attach = [];
                    $order = 0;
                    foreach ($data['ingredients'] as $name => $quantity) {
                        $ingredient = Ingredient::firstOrCreate(['name' => $name]);
                        $attach[$ingredient->id] = ['quantity' => $quantity, 'sort_order' => $order++];
                    }
                    $meal->ingredients()->sync($attach);

                    $meal->cookingSteps()->delete();
                    foreach ($data['steps'] as $n => $step) {
                        $meal->cookingSteps()->create([
                            'step_number' => $n + 1,
                            'title' => $step[0],
                            'instruction' => $step[1],
                            'duration_minutes' => $step[2] ?? null,
                        ]);
                    }
                }
            }
        }
    }
}
