@extends('layouts.site')

@section('title', 'The Menu — 135 Macro-Counted Meals | LaFitMeal')

@section('content')
<section class="mx-auto max-w-7xl px-5 pb-10 pt-32 lg:px-8">
    <p data-reveal class="chip">The full lineup</p>
    <h1 data-reveal class="h-display mt-5 text-5xl md:text-7xl">The <span class="text-lime-neon">menu</span></h1>
    <p data-reveal class="mt-5 max-w-xl text-cream-dim">Every meal below ships with complete macros, the full ingredient list and the exact cooking method. Nothing to hide.</p>

    @if (filled($q))
        <p data-reveal class="mt-6 flex flex-wrap items-center gap-3 text-sm text-cream-dim">
            <span>Results for <span class="font-semibold text-cream">“{{ $q }}”</span></span>
            <a href="{{ route('menu', array_filter(['category' => $category, 'meal_time' => $mealTime])) }}"
               class="rounded-full border border-ink-line px-3 py-1 text-xs font-semibold transition hover:border-lime-neon hover:text-lime-neon">Clear ✕</a>
        </p>
    @endif

    {{-- filters --}}
    <div data-reveal class="mt-10 flex flex-wrap items-center gap-3">
        @php $cat = $category; $mt = $mealTime; @endphp
        @foreach ([null => 'All', 'veg' => '🥬 Veg', 'non_veg' => '🍗 Non-Veg', 'vegan' => '🌱 Vegan'] as $value => $label)
            <a href="{{ route('menu', array_filter(['category' => $value, 'meal_time' => $mt, 'q' => $q])) }}"
               class="rounded-full border px-5 py-2.5 text-sm font-semibold transition-all duration-300
                      {{ $cat === $value ? 'border-lime-neon bg-lime-neon text-ink' : 'border-ink-line text-cream-dim hover:border-lime-neon hover:text-lime-neon' }}">
                {{ $label }}
            </a>
        @endforeach
        <span class="mx-2 hidden h-6 w-px bg-ink-line sm:block"></span>
        @foreach ([null => 'Any time', 'breakfast' => '☀️ Breakfast', 'lunch' => '🌤️ Lunch', 'dinner' => '🌙 Dinner'] as $value => $label)
            <a href="{{ route('menu', array_filter(['category' => $cat, 'meal_time' => $value, 'q' => $q])) }}"
               class="rounded-full border px-5 py-2.5 text-sm font-semibold transition-all duration-300
                      {{ $mt === $value ? 'border-mint bg-mint text-ink' : 'border-ink-line text-cream-dim hover:border-mint hover:text-mint' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</section>

<section class="mx-auto max-w-7xl px-5 pb-24 lg:px-8">
    <p class="mb-6 text-sm text-cream-dim">{{ $meals->count() }} meals</p>
    <div data-stagger class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($meals as $meal)
            <x-meal-card :meal="$meal" />
        @empty
            <p class="col-span-full py-20 text-center text-cream-dim">
                @if (filled($q))
                    No meals match “{{ $q }}”. Try a dish, an ingredient or a flavour.
                @else
                    No meals match those filters.
                @endif
            </p>
        @endforelse
    </div>
</section>
@endsection
