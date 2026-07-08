@extends('layouts.site')

@section('content')

{{-- ============ HERO ============ --}}
<section data-hero class="relative flex min-h-screen items-center overflow-hidden">
    {{-- 3D particle bowl --}}
    <div class="absolute inset-0">
        <canvas id="hero-canvas" class="h-full w-full"></canvas>
    </div>

    {{-- radial vignette --}}
    <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(ellipse at center, transparent 30%, #0b120d 85%);"></div>

    {{-- floating ingredients --}}
    <span data-depth="0.9" class="pointer-events-none absolute left-[8%] top-[22%] animate-float text-5xl opacity-80" style="animation-delay:-1s">🥦</span>
    <span data-depth="1.4" class="pointer-events-none absolute right-[12%] top-[18%] animate-float text-6xl opacity-80" style="animation-delay:-3s">🍅</span>
    <span data-depth="0.7" class="pointer-events-none absolute left-[16%] bottom-[24%] animate-float text-4xl opacity-70" style="animation-delay:-5s">🌶️</span>
    <span data-depth="1.1" class="pointer-events-none absolute right-[18%] bottom-[30%] animate-float text-5xl opacity-70" style="animation-delay:-2s">🥕</span>
    <span data-depth="1.8" class="pointer-events-none absolute left-[42%] top-[12%] animate-float text-3xl opacity-60" style="animation-delay:-4s">🌿</span>

    <div class="relative z-10 mx-auto w-full max-w-7xl px-5 pt-24 lg:px-8">
        <div class="max-w-4xl">
            <div class="overflow-hidden"><p data-hero-line class="chip mb-6">⚡ Subscription meals · Veg / Non-Veg / Vegan</p></div>
            <h1 class="h-display text-[13vw] md:text-8xl">
                <span class="block overflow-hidden"><span data-hero-line class="block">Eat like</span></span>
                <span class="block overflow-hidden"><span data-hero-line class="block">it's <span class="text-lime-neon">designed</span></span></span>
                <span class="block overflow-hidden"><span data-hero-line class="block text-outline">for your body</span></span>
            </h1>
            <p data-hero-fade class="mt-7 max-w-xl text-lg leading-relaxed text-cream-dim">
                135+ chef-crafted meals with every macro counted. Build your weekly plan,
                skip any day before cutoff, and your wallet keeps the change. Minimum 15-day plan — maximum life upgrade.
            </p>
            <div data-hero-fade class="mt-10 flex flex-wrap items-center gap-4">
                <a href="{{ route(auth()->check() ? 'plan.create' : 'register') }}" data-magnet class="btn-primary animate-pulse-glow text-base">Build my plan →</a>
                <a href="{{ route('menu') }}" data-magnet class="btn-ghost text-base">Explore the menu</a>
            </div>
            <div data-hero-fade class="mt-14 flex flex-wrap gap-10 text-sm text-cream-dim">
                <div><p class="font-display text-3xl font-bold text-cream"><span data-count="{{ $stats['meals'] }}">0</span>+</p><p class="mt-1">unique meals</p></div>
                <div><p class="font-display text-3xl font-bold text-cream"><span data-count="{{ $stats['avgProtein'] }}">0</span>g</p><p class="mt-1">avg. protein / meal</p></div>
                <div><p class="font-display text-3xl font-bold text-cream"><span data-count="15">0</span> days</p><p class="mt-1">minimum plan</p></div>
            </div>
        </div>
    </div>

    {{-- scroll cue --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-cream-dim/60">
        <div class="flex h-10 w-6 items-start justify-center rounded-full border border-ink-line p-1.5">
            <span class="h-2 w-1 animate-bounce rounded-full bg-lime-neon"></span>
        </div>
    </div>
</section>

{{-- ============ MARQUEE ============ --}}
<section class="overflow-hidden border-y border-ink-line bg-ink-soft py-5">
    <div class="flex w-max animate-marquee gap-8 whitespace-nowrap font-display text-sm font-bold uppercase tracking-[0.25em] text-cream-dim">
        @for ($i = 0; $i < 2; $i++)
            <span>High Protein</span><span class="text-lime-neon">✦</span>
            <span>Macro Counted</span><span class="text-coral">✦</span>
            <span>Skip Any Day</span><span class="text-mint">✦</span>
            <span>Wallet Credits</span><span class="text-lime-neon">✦</span>
            <span>Chef Crafted</span><span class="text-coral">✦</span>
            <span>Zero Guesswork</span><span class="text-mint">✦</span>
        @endfor
    </div>
</section>

{{-- ============ CATEGORY WORLDS ============ --}}
<section class="mx-auto max-w-7xl px-5 py-28 lg:px-8">
    <p data-reveal class="chip">Choose your world</p>
    <h2 data-reveal class="h-display mt-5 max-w-3xl text-4xl md:text-6xl">Three kitchens.<br><span class="font-accent normal-case italic text-lime-neon" style="letter-spacing:0">one obsession</span> — your macros.</h2>

    <div data-stagger class="mt-14 grid gap-6 md:grid-cols-3">
        @foreach ([
            ['veg', 'Veg', '🥬', 'Paneer power bowls, dal thalis, millet dosas — vegetarian food that lifts.', 'text-lime-neon'],
            ['non_veg', 'Non-Veg', '🍗', 'Grilled chicken, tandoori fish, prawn stir-fries — lean protein, serious flavour.', 'text-coral'],
            ['vegan', 'Vegan', '🌱', 'Tofu tikkas, buddha bowls, cashew dal makhani — 100% plants, 0% compromise.', 'text-mint'],
        ] as [$slug, $label, $emoji, $desc, $color])
            <a href="{{ route('menu', ['category' => $slug]) }}"
               class="tilt cat-{{ $slug }} cat-glow group relative overflow-hidden rounded-3xl border border-ink-line bg-ink-card p-8 transition-shadow duration-500">
                <span class="tilt-pop block text-6xl transition-transform duration-500 group-hover:scale-125 group-hover:-rotate-6">{{ $emoji }}</span>
                <h3 class="mt-6 font-display text-2xl font-bold uppercase {{ $color }}">{{ $label }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-cream-dim">{{ $desc }}</p>
                <p class="mt-6 text-sm font-semibold {{ $color }}">45 meals → </p>
                <span class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full blur-3xl" style="background: rgb(var(--cat) / .18)"></span>
            </a>
        @endforeach
    </div>
</section>

{{-- ============ FEATURED MEALS ============ --}}
<section class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p data-reveal class="chip">Tonight's stars</p>
            <h2 data-reveal class="h-display mt-5 text-4xl md:text-5xl">Featured <span class="text-outline">meals</span></h2>
        </div>
        <a data-reveal href="{{ route('menu') }}" class="btn-ghost">All 135 meals →</a>
    </div>

    <div data-stagger class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($featured as $meal)
            <x-meal-card :meal="$meal" />
        @endforeach
    </div>
</section>

{{-- ============ HOW IT WORKS ============ --}}
<section class="mx-auto max-w-7xl px-5 py-28 lg:px-8">
    <p data-reveal class="chip">Dead simple</p>
    <h2 data-reveal class="h-display mt-5 text-4xl md:text-6xl">How it <span class="font-accent normal-case italic text-lime-neon" style="letter-spacing:0">works</span></h2>

    <div class="mt-16 grid gap-6 md:grid-cols-4">
        @foreach ([
            ['01', 'Pick your world', 'Veg, Non-Veg or Vegan — choose the kitchen that matches your plate.'],
            ['02', 'Design your week', 'Toggle breakfast, lunch & dinner for each day. Duplicate the week across your plan.'],
            ['03', 'We cook & deliver', 'Fresh, macro-counted meals at your door. Every single scheduled day.'],
            ['04', 'Skip? Get credited', 'Life happens. Skip before cutoff and the meal\'s value lands in your wallet.'],
        ] as $i => [$num, $title, $desc])
            <div data-reveal data-delay="{{ $i * 0.12 }}" class="glass relative p-7">
                <span class="font-display text-5xl font-black text-outline">{{ $num }}</span>
                <h3 class="mt-5 font-display text-base font-bold uppercase tracking-wide">{{ $title }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-cream-dim">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="relative mx-auto max-w-7xl overflow-hidden px-5 pb-20 lg:px-8">
    <div data-reveal="zoom" class="relative overflow-hidden rounded-[2.5rem] border border-ink-line bg-gradient-to-br from-ink-card via-ink-soft to-ink p-12 text-center md:p-20">
        <span class="pointer-events-none absolute left-1/4 top-0 h-64 w-64 -translate-y-1/2 rounded-full bg-lime-neon/15 blur-3xl"></span>
        <span class="pointer-events-none absolute bottom-0 right-1/4 h-64 w-64 translate-y-1/2 rounded-full bg-mint/10 blur-3xl"></span>
        <h2 class="h-display text-4xl md:text-6xl">Your body is<br>the <span class="text-lime-neon">project</span>.</h2>
        <p class="mx-auto mt-6 max-w-md text-cream-dim">Start a 15-day plan today. Skip days freely. Every rupee of skipped meals stays yours.</p>
        <a href="{{ route(auth()->check() ? 'plan.create' : 'register') }}" data-magnet class="btn-primary mt-10 text-base">Build my plan →</a>
    </div>
</section>

@endsection
