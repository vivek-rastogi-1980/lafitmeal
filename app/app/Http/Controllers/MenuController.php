<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function home()
    {
        $featured = Meal::active()->where('is_featured', true)->orderBy('sort_order')->take(6)->get();
        $stats = [
            'meals' => Meal::active()->count(),
            'avgProtein' => (int) Meal::active()->avg('protein_g'),
        ];

        return view('home', compact('featured', 'stats'));
    }

    public function index(Request $request)
    {
        $category = $request->query('category');
        $mealTime = $request->query('meal_time');

        $meals = Meal::active()
            ->when(in_array($category, Meal::CATEGORIES), fn ($q) => $q->where('category', $category))
            ->when(in_array($mealTime, Meal::MEAL_TIMES), fn ($q) => $q->where('meal_time', $mealTime))
            ->orderBy('sort_order')
            ->get();

        return view('menu.index', compact('meals', 'category', 'mealTime'));
    }

    public function show(Meal $meal)
    {
        abort_unless($meal->is_active, 404);

        $meal->load(['ingredients', 'cookingSteps']);
        $related = Meal::active()
            ->where('category', $meal->category)
            ->where('meal_time', $meal->meal_time)
            ->where('id', '!=', $meal->id)
            ->take(3)->get();

        return view('menu.show', compact('meal', 'related'));
    }
}
