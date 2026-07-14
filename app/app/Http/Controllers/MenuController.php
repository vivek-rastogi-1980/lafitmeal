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

        // Meals shown inside the hero's rotating glass carousel
        $heroMeal = Meal::where('slug', 'paneer-tikka-quinoa-bowl')->first();
        $carouselSlugs = [
            'paneer-tikka-quinoa-bowl',
            'grilled-lemon-herb-chicken-sauteed-greens',
            'crispy-tofu-buddha-bowl',
            'baked-salmon-with-quinoa-asparagus',
            'rainbow-buddha-bowl',
            'berry-acai-smoothie-bowl',
        ];
        $heroCarousel = Meal::whereIn('slug', $carouselSlugs)->get()
            ->sortBy(fn ($m) => array_search($m->slug, $carouselSlugs))->values();

        // One signature dish photo per kitchen for the category cards
        $categoryShowcase = [
            'veg' => Meal::where('slug', 'palak-paneer-power-bowl')->first(),
            'non_veg' => Meal::where('slug', 'grilled-lemon-herb-chicken-sauteed-greens')->first(),
            'vegan' => Meal::where('slug', 'crispy-tofu-buddha-bowl')->first(),
        ];

        return view('home', compact('featured', 'stats', 'heroMeal', 'heroCarousel', 'categoryShowcase'));
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
