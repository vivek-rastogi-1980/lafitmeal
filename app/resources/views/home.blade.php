@extends('layouts.site')

@section('content')

{{-- ============ HERO — person selecting from orbiting glass dishes ============ --}}
<section data-hero class="relative flex min-h-screen items-center overflow-hidden">
    {{-- ambient backdrop --}}
    <div class="pointer-events-none absolute inset-0">
        @if ($heroMeal?->image_url)
            <img src="{{ $heroMeal->image_url }}" alt="" aria-hidden="true"
                 class="h-full w-full scale-110 object-cover opacity-20 blur-2xl saturate-[1.15]">
        @endif
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at 50% 42%, transparent 0%, rgba(11,18,13,.72) 55%, #0b120d 90%);"></div>
        <span class="absolute -left-24 top-1/4 h-96 w-96 rounded-full bg-lime-neon/10 blur-3xl"></span>
        <span class="absolute -right-24 bottom-1/4 h-96 w-96 rounded-full bg-mint/10 blur-3xl"></span>
    </div>

    <div class="relative z-10 mx-auto grid w-full max-w-7xl items-center gap-8 px-5 pb-20 pt-28 lg:grid-cols-[0.9fr_1.1fr] lg:gap-6 lg:px-8">
        {{-- ===== left: headline ===== --}}
        <div class="text-center lg:text-left">
            <div class="overflow-hidden"><p data-hero-line class="chip mb-6">⚡ Subscription meals · Veg / Non-Veg / Vegan</p></div>
            <h1 class="h-display text-[12vw] sm:text-6xl xl:text-7xl">
                <span class="block overflow-hidden"><span data-hero-line class="block">Eat like</span></span>
                <span class="block overflow-hidden"><span data-hero-line class="block">it's <span class="text-lime-neon">designed</span></span></span>
                <span class="block overflow-hidden"><span data-hero-line class="block text-outline">for your body</span></span>
            </h1>
            <p data-hero-fade class="mx-auto mt-7 max-w-md text-lg leading-relaxed text-cream-dim lg:mx-0">
                Spin through 135+ chef-crafted meals, pick what fuels you, and we deliver.
                Skip any day before cutoff — your wallet keeps the change.
            </p>
            <div data-hero-fade class="mt-10 flex flex-wrap items-center justify-center gap-4 lg:justify-start">
                <a href="{{ route(auth()->check() ? 'plan.create' : 'register') }}" data-magnet class="btn-primary animate-pulse-glow text-base">Build my plan →</a>
                <a href="{{ route('menu') }}" data-magnet class="btn-ghost text-base">Explore the menu</a>
            </div>
            <div data-hero-fade class="mt-12 flex flex-wrap justify-center gap-10 text-sm text-cream-dim lg:justify-start">
                <div><p class="font-display text-3xl font-bold text-cream"><span data-count="{{ $stats['meals'] }}">0</span>+</p><p class="mt-1">unique meals</p></div>
                <div><p class="font-display text-3xl font-bold text-cream"><span data-count="{{ $stats['avgProtein'] }}">0</span>g</p><p class="mt-1">avg. protein / meal</p></div>
                <div><p class="font-display text-3xl font-bold text-cream"><span data-count="15">0</span> days</p><p class="mt-1">minimum plan</p></div>
            </div>
        </div>

        {{-- ===== right: 3D orbit stage — person + rotating dish cards ===== --}}
        <div data-hero-fade class="relative">
            <div data-hero-stage class="relative mx-auto h-[520px] w-full max-w-xl select-none sm:h-[600px]">

                {{-- floor glow the person stands on --}}
                <div class="pointer-events-none absolute bottom-16 left-1/2 h-16 w-64 -translate-x-1/2 rounded-[100%] bg-lime-neon/20 blur-2xl"></div>

                {{-- orbiting dish cards --}}
                <div data-hero-ring class="absolute inset-0">
                    @foreach ($heroCarousel as $dish)
                        <a href="{{ route('meals.show', $dish) }}" data-dish
                           data-name="{{ $dish->name }}"
                           data-cat="{{ $dish->category_label }}"
                           data-kcal="{{ $dish->calories }}"
                           data-protein="{{ $dish->protein_g }}"
                           class="dish-card-3d glass-lux glass-shimmer cat-{{ $dish->category }} absolute left-1/2 top-1/2 w-[132px] overflow-hidden sm:w-[164px]">
                            <div class="relative aspect-square overflow-hidden rounded-t-3xl">
                                @if ($dish->image_url)
                                    <img src="{{ $dish->image_url }}" alt="{{ $dish->name }}" loading="lazy" class="food-photo h-full w-full object-cover">
                                @endif
                                <span class="cat-badge absolute left-2 top-2 rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider">{{ $dish->category_label }}</span>
                            </div>
                            <div class="p-3">
                                <p class="truncate font-display text-[11px] font-bold uppercase leading-tight">{{ $dish->name }}</p>
                                <div class="mt-1.5 flex items-center justify-between text-[10px] text-cream-dim">
                                    <span>🔥 {{ $dish->calories }}</span>
                                    <span class="cat-text font-semibold">{{ $dish->protein_g }}g protein</span>
                                </div>
                            </div>
                            {{-- selection reticle (shown on the front card) --}}
                            <span class="dish-pick pointer-events-none absolute inset-0 rounded-3xl"></span>
                        </a>
                    @endforeach
                </div>

                {{-- HUD: what the person is currently selecting --}}
                <div data-hero-hud class="glass-lux absolute bottom-2 left-1/2 z-[120] flex w-[min(92%,360px)] -translate-x-1/2 items-center gap-3 p-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-lime-neon/15 text-lg">🍽️</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] uppercase tracking-widest text-lime-neon">Selecting…</p>
                        <p data-hud-name class="truncate text-sm font-bold">—</p>
                        <p data-hud-macros class="truncate text-[11px] text-cream-dim">—</p>
                    </div>
                    <a href="{{ route(auth()->check() ? 'plan.create' : 'register') }}"
                       class="shrink-0 rounded-full bg-lime-neon px-4 py-2 text-xs font-bold text-ink transition hover:shadow-[0_0_24px_rgba(198,242,78,.5)]">
                        + Add
                    </a>
                </div>

                {{-- manual spin controls --}}
                <button data-hero-prev aria-label="Previous dish" class="glass-lux absolute left-0 top-1/2 z-[130] grid h-10 w-10 -translate-y-1/2 place-items-center text-lg text-cream transition hover:text-lime-neon">‹</button>
                <button data-hero-next aria-label="Next dish" class="glass-lux absolute right-0 top-1/2 z-[130] grid h-10 w-10 -translate-y-1/2 place-items-center text-lg text-cream transition hover:text-lime-neon">›</button>
            </div>
        </div>
    </div>

    {{-- scroll cue --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-cream-dim/60">
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
            @php $showcase = $categoryShowcase[$slug] ?? null; @endphp
            <a href="{{ route('menu', ['category' => $slug]) }}"
               class="tilt cat-{{ $slug }} cat-glow glass-lux glass-shimmer group relative block overflow-hidden transition-shadow duration-500">
                {{-- dish photo --}}
                <div class="relative aspect-[16/10] overflow-hidden rounded-t-3xl">
                    @if ($showcase?->video_url)
                        <video data-food-video poster="{{ $showcase->image_url }}"
                               autoplay muted playsinline preload="metadata"
                               aria-label="{{ $label }} — {{ $showcase->name }}"
                               class="h-full w-full object-cover transition duration-500 group-hover:brightness-110">
                            <source src="{{ $showcase->video_url }}" type="video/mp4">
                        </video>
                    @elseif ($showcase?->image_url)
                        <img src="{{ $showcase->image_url }}" alt="{{ $label }} — {{ $showcase->name }}" loading="lazy"
                             class="food-photo h-full w-full object-cover transition duration-500 group-hover:brightness-110">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/15 to-transparent"></div>
                    <span class="absolute left-4 top-4 rounded-full bg-ink/60 px-3 py-1 text-lg backdrop-blur">{{ $emoji }}</span>
                    <span class="cat-badge absolute bottom-4 left-4 rounded-full px-3.5 py-1 text-[11px] font-bold uppercase tracking-wider">45 meals</span>
                </div>
                {{-- glass body --}}
                <div class="p-6">
                    <h3 class="font-display text-2xl font-bold uppercase {{ $color }}">{{ $label }}</h3>
                    <p class="mt-2.5 text-sm leading-relaxed text-cream-dim">{{ $desc }}</p>
                    <p class="mt-5 text-sm font-semibold {{ $color }}">Explore the kitchen →</p>
                </div>
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
