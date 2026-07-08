@extends('layouts.site')

@section('title', $meal->name . ' — Nutrition, Ingredients & Recipe | LaFitMeal')
@section('meta_description', Str::limit($meal->description, 150))

@section('content')
<article class="cat-{{ $meal->category }}">

    {{-- ======== HERO ======== --}}
    <section class="relative overflow-hidden pt-32">
        <span class="pointer-events-none absolute -top-32 right-0 h-[32rem] w-[32rem] rounded-full blur-3xl" style="background: rgb(var(--cat) / .12)"></span>

        <div class="mx-auto grid max-w-7xl items-center gap-14 px-5 pb-16 lg:grid-cols-2 lg:px-8">
            <div>
                <div data-reveal class="flex flex-wrap gap-2.5">
                    <span class="cat-bg rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-ink">{{ $meal->category_label }}</span>
                    <span class="chip">{{ ucfirst($meal->meal_time) }}</span>
                    @if ($meal->badge)<span class="chip">{{ $meal->badge }}</span>@endif
                </div>
                <h1 data-reveal class="h-display mt-6 text-4xl md:text-6xl">{{ $meal->name }}</h1>
                <p data-reveal class="mt-4 font-accent text-2xl italic text-cream-dim">{{ $meal->tagline }}</p>
                <p data-reveal class="mt-6 max-w-lg leading-relaxed text-cream-dim">{{ $meal->description }}</p>

                <div data-reveal class="mt-8 flex flex-wrap items-center gap-6">
                    <span class="cat-text font-display text-4xl font-bold">₹{{ number_format($meal->price) }}</span>
                    <span class="text-sm text-cream-dim">⏱️ {{ $meal->prep_time_minutes + $meal->cook_time_minutes }} min total ·
                        {{ str_repeat('🌶️', $meal->spice_level) }}</span>
                </div>

                <div data-reveal class="mt-9 flex flex-wrap gap-4">
                    <a href="{{ route(auth()->check() ? 'plan.create' : 'register') }}" data-magnet class="btn-primary">Add to my plan →</a>
                    <a href="{{ route('menu', ['category' => $meal->category]) }}" data-magnet class="btn-ghost">More {{ $meal->category_label }}</a>
                </div>
            </div>

            {{-- rotating plate presentation --}}
            <div data-reveal="zoom" class="relative mx-auto aspect-square w-full max-w-md">
                <div class="absolute inset-0 animate-spin-slow rounded-full border border-dashed border-ink-line"></div>
                <div class="absolute inset-6 animate-spin-slow rounded-full border border-ink-line/50" style="animation-direction: reverse; animation-duration: 20s;"></div>
                <div class="tilt absolute inset-12 overflow-hidden rounded-full border-2 shadow-2xl cat-border"
                     style="box-shadow: 0 40px 120px -20px rgb(var(--cat) / .4);">
                    @if ($meal->image_url)
                        <img src="{{ $meal->image_url }}" alt="{{ $meal->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="grid h-full w-full place-items-center"
                             style="background: radial-gradient(circle at 35% 30%, rgb(var(--cat) / .35), #101a13 70%);">
                            <span class="tilt-pop animate-float text-[7rem] drop-shadow-2xl">{{ $meal->emoji }}</span>
                        </div>
                    @endif
                </div>
                {{-- orbiting macro satellites --}}
                <span class="glass absolute -left-2 top-1/4 px-4 py-2 text-xs font-bold" data-depth="1.2">🔥 {{ $meal->calories }} kcal</span>
                <span class="glass absolute -right-2 top-1/2 px-4 py-2 text-xs font-bold" data-depth="0.8">💪 {{ $meal->protein_g }}g protein</span>
                <span class="glass absolute bottom-6 left-1/4 px-4 py-2 text-xs font-bold" data-depth="1.5">🌾 {{ $meal->fiber_g }}g fiber</span>
            </div>
        </div>
    </section>

    {{-- ======== MACRO RINGS ======== --}}
    <section class="border-y border-ink-line bg-ink-soft">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-5 py-14 md:grid-cols-5 lg:px-8">
            @foreach ([
                ['Calories', $meal->calories, 800, 'kcal', '#c6f24e'],
                ['Protein', $meal->protein_g, 50, 'g', '#4adeb0'],
                ['Carbs', $meal->carbs_g, 100, 'g', '#f2c94c'],
                ['Fat', $meal->fat_g, 40, 'g', '#ff7a59'],
                ['Fiber', $meal->fiber_g, 20, 'g', '#8fc428'],
            ] as [$label, $value, $max, $unit, $color])
                <div class="flex flex-col items-center gap-3">
                    <div class="relative h-24 w-24">
                        <svg viewBox="0 0 64 64" class="h-full w-full -rotate-90">
                            <circle cx="32" cy="32" r="26" fill="none" stroke-width="5" class="ring-track"/>
                            <circle cx="32" cy="32" r="26" fill="none" stroke-width="5" class="ring-fill"
                                    stroke="{{ $color }}" data-pct="{{ min((float) $value / $max, 1) }}"/>
                        </svg>
                        <span class="absolute inset-0 grid place-items-center text-sm font-bold">
                            <span><span data-count="{{ $value }}" data-decimals="{{ (int) $value != $value ? 1 : 0 }}">0</span>{{ $unit === 'g' ? 'g' : '' }}</span>
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-cream-dim">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ======== INGREDIENTS + METHOD ======== --}}
    <section class="mx-auto grid max-w-7xl gap-16 px-5 py-24 lg:grid-cols-5 lg:px-8">
        {{-- ingredients --}}
        <div class="lg:col-span-2">
            <p data-reveal class="chip">What's inside</p>
            <h2 data-reveal class="h-display mt-4 text-3xl">Ingredients</h2>
            <ul data-stagger class="mt-8 space-y-3">
                @foreach ($meal->ingredients as $ingredient)
                    <li class="glass flex items-center justify-between px-5 py-3.5 text-sm">
                        <span class="font-medium">{{ $ingredient->name }}</span>
                        <span class="cat-text font-semibold">{{ $ingredient->pivot->quantity }}</span>
                    </li>
                @endforeach
            </ul>
            @if ($meal->allergens)
                <p class="mt-6 text-xs uppercase tracking-widest text-cream-dim">
                    ⚠️ Allergens: {{ implode(', ', $meal->allergens) }}
                </p>
            @endif
        </div>

        {{-- cooking steps timeline --}}
        <div class="lg:col-span-3">
            <p data-reveal class="chip">How we cook it</p>
            <h2 data-reveal class="h-display mt-4 text-3xl">The method</h2>
            <ol class="relative mt-10 space-y-8 border-l border-ink-line pl-8">
                @foreach ($meal->cookingSteps as $step)
                    <li data-reveal data-delay="{{ $loop->index * 0.08 }}" class="relative">
                        <span class="cat-bg absolute -left-[41px] grid h-6 w-6 place-items-center rounded-full text-[11px] font-black text-ink">{{ $step->step_number }}</span>
                        <div class="glass p-6">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="font-display text-sm font-bold uppercase tracking-wide">{{ $step->title }}</h3>
                                @if ($step->duration_minutes)
                                    <span class="chip whitespace-nowrap">⏱ {{ $step->duration_minutes }} min</span>
                                @endif
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-cream-dim">{{ $step->instruction }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ======== RELATED ======== --}}
    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-7xl px-5 pb-24 lg:px-8">
            <h2 data-reveal class="h-display text-3xl">You might also <span class="cat-text">crave</span></h2>
            <div data-stagger class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $rel)
                    <x-meal-card :meal="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</article>
@endsection
