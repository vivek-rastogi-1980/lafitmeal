@extends('layouts.site')

@section('title', 'How It Works | LaFitMeal')

@section('content')
<section class="mx-auto max-w-7xl px-5 pb-10 pt-32 lg:px-8">
    <p data-reveal class="chip">The system</p>
    <h1 data-reveal class="h-display mt-5 max-w-3xl text-5xl md:text-7xl">Simple on the <span class="text-lime-neon">surface</span>,<br><span class="text-outline">smart underneath</span></h1>
</section>

<section class="mx-auto max-w-7xl space-y-8 px-5 py-16 lg:px-8">
    @foreach ([
        ['01', '🥬', 'Pick your kitchen', 'Choose Veg, Non-Veg or Vegan when you sign up. 45 chef-designed meals per kitchen — 15 breakfasts, 15 lunches, 15 dinners — each with full macros, ingredients and method published.', 'left'],
        ['02', '🗓️', 'Design your week', 'Toggle breakfast, lunch and dinner for each day of the week. Swap any dish in any slot. Your weekly design repeats across the whole plan — duplicate once, eat all month.', 'right'],
        ['03', '💳', 'Pay once, minimum 15 days', 'Plans start at 15 days. The live calculator prices exactly the meals you enabled — no averages, no bundles you didn\'t ask for.', 'left'],
        ['04', '🚚', 'We cook & deliver daily', 'Every scheduled day, your meals are cooked fresh and delivered to your door. Track everything from your dashboard.', 'right'],
        ['05', '⏭️', 'Skip freely, before cutoff', 'Travelling? Eating out? Skip one meal or a whole day before the cutoff. The exact value of every skipped meal is credited to your wallet instantly.', 'left'],
        ['06', '👛', 'Wallet pays you forward', 'Wallet credits auto-apply when you renew or start a new plan. Money you didn\'t eat stays your money.', 'right'],
    ] as [$num, $emoji, $title, $desc, $side])
        <div data-reveal="{{ $side }}" class="glass flex flex-col gap-6 p-8 md:flex-row md:items-center md:gap-10 md:p-12 {{ $side === 'right' ? 'md:flex-row-reverse' : '' }}">
            <div class="flex shrink-0 items-center gap-5">
                <span class="font-display text-6xl font-black text-outline">{{ $num }}</span>
                <span class="text-6xl">{{ $emoji }}</span>
            </div>
            <div>
                <h2 class="font-display text-xl font-bold uppercase tracking-wide">{{ $title }}</h2>
                <p class="mt-3 max-w-2xl leading-relaxed text-cream-dim">{{ $desc }}</p>
            </div>
        </div>
    @endforeach
</section>

<section class="mx-auto max-w-7xl px-5 pb-24 lg:px-8">
    <div data-reveal="zoom" class="rounded-[2.5rem] border border-ink-line bg-gradient-to-br from-ink-card to-ink p-12 text-center md:p-16">
        <h2 class="h-display text-3xl md:text-5xl">Ready to eat <span class="text-lime-neon">on purpose</span>?</h2>
        <a href="{{ route(auth()->check() ? 'plan.create' : 'register') }}" data-magnet class="btn-primary mt-8">Build my plan →</a>
    </div>
</section>
@endsection
