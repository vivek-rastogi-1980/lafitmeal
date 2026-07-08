@extends('layouts.site')

@section('title', 'Your Dashboard | LaFitMeal')

@section('content')
<section class="mx-auto max-w-7xl px-5 pb-24 pt-32 lg:px-8">

    {{-- flash messages --}}
    @if (session('status') === 'plan-activated')
        <div class="glass mb-8 flex items-center gap-3 border-lime-neon/50 p-5 text-sm"><span class="text-2xl">🎉</span> Your plan is live! First delivery lands on {{ $subscription?->start_date->format('D, d M') }}.</div>
    @elseif (session('status') === 'meal-skipped')
        <div class="glass mb-8 flex items-center gap-3 border-mint/50 p-5 text-sm"><span class="text-2xl">💸</span> Meal skipped — its value has been credited to your wallet.</div>
    @elseif (session('status') === 'meal-restored')
        <div class="glass mb-8 flex items-center gap-3 border-lime-neon/50 p-5 text-sm"><span class="text-2xl">✅</span> Meal re-enabled — the wallet credit was reversed.</div>
    @elseif (session('status') === 'day-skipped')
        <div class="glass mb-8 flex items-center gap-3 border-mint/50 p-5 text-sm"><span class="text-2xl">💸</span> Day skipped — all meal values credited to your wallet.</div>
    @endif
    @if ($errors->any())
        <div class="glass mb-8 border-coral/50 p-5 text-sm text-coral">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="chip">Welcome back</p>
            <h1 class="h-display mt-4 text-4xl md:text-5xl">Hey, <span class="text-lime-neon">{{ explode(' ', auth()->user()->name)[0] }}</span></h1>
        </div>
        <a href="{{ route('wallet') }}" class="glass group flex items-center gap-4 px-6 py-4 transition hover:border-lime-neon">
            <span class="text-3xl">👛</span>
            <div>
                <p class="text-xs uppercase tracking-widest text-cream-dim">Wallet balance</p>
                <p class="font-display text-2xl font-bold text-lime-neon">₹<span data-count="{{ $wallet->balance }}">0</span></p>
            </div>
        </a>
    </div>

    @if (! $subscription || $subscription->status !== 'active')
        {{-- no active plan --}}
        <div class="glass mt-12 p-12 text-center md:p-20">
            <span class="text-6xl">🍽️</span>
            <h2 class="h-display mt-6 text-3xl">No active plan yet</h2>
            <p class="mx-auto mt-4 max-w-md text-cream-dim">Design your week once, and we handle every meal after that. Minimum 15 days, skip whenever life happens.</p>
            <a href="{{ route('plan.create') }}" data-magnet class="btn-primary mt-8">Build my plan →</a>
        </div>
    @else
        {{-- plan summary --}}
        <div data-stagger class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="glass p-6">
                <p class="text-xs uppercase tracking-widest text-cream-dim">Plan</p>
                <p class="mt-2 font-display text-xl font-bold uppercase">{{ str_replace('_', '-', $subscription->meal_category) }} · {{ $subscription->duration_days }}d</p>
            </div>
            <div class="glass p-6">
                <p class="text-xs uppercase tracking-widest text-cream-dim">Runs till</p>
                <p class="mt-2 font-display text-xl font-bold">{{ $subscription->end_date->format('d M Y') }}</p>
            </div>
            <div class="glass p-6">
                <p class="text-xs uppercase tracking-widest text-cream-dim">Days left</p>
                <p class="mt-2 font-display text-xl font-bold text-lime-neon">{{ max(now()->startOfDay()->diffInDays($subscription->end_date, false) + 1, 0) }}</p>
            </div>
            <div class="glass p-6">
                <p class="text-xs uppercase tracking-widest text-cream-dim">Delivering to</p>
                <p class="mt-2 truncate text-sm font-semibold">{{ $subscription->address?->full_address ?? '—' }}</p>
            </div>
        </div>

        {{-- today --}}
        @if ($today->isNotEmpty())
            <h2 class="h-display mt-16 text-2xl">Today's <span class="text-lime-neon">plate</span></h2>
            <div data-stagger class="mt-6 grid gap-5 md:grid-cols-3">
                @foreach ($today as $item)
                    <div class="glass cat-{{ $item->meal?->category ?? 'veg' }} flex items-center gap-4 p-5 {{ $item->status === 'skipped' ? 'opacity-50' : '' }}">
                        <span class="text-4xl">{{ $item->meal?->emoji }}</span>
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-widest text-cream-dim">{{ ucfirst($item->meal_time) }}</p>
                            <p class="truncate font-semibold">{{ $item->meal?->name }}</p>
                            <p class="mt-0.5 text-xs {{ $item->status === 'skipped' ? 'text-coral' : 'cat-text' }}">{{ ucwords(str_replace('_', ' ', $item->status)) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- upcoming calendar --}}
        <div class="mt-16 flex flex-wrap items-center justify-between gap-4">
            <h2 class="h-display text-2xl">Upcoming <span class="text-lime-neon">deliveries</span></h2>
            <p class="text-xs text-cream-dim">Skip before cutoff ({{ \App\Models\Setting::get('skip_cutoff_hours', 12) }}h prior) and the value returns to your wallet.</p>
        </div>

        <div class="mt-8 space-y-6">
            @foreach ($schedulesByDate->take(10) as $date => $items)
                @php $d = \Illuminate\Support\Carbon::parse($date); @endphp
                <div data-reveal class="glass overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-line bg-ink-soft/60 px-6 py-4">
                        <p class="font-display text-sm font-bold uppercase tracking-widest">
                            {{ $d->isToday() ? '🔥 Today' : ($d->isTomorrow() ? 'Tomorrow' : $d->format('l')) }}
                            <span class="ml-2 font-sans font-normal normal-case tracking-normal text-cream-dim">{{ $d->format('d M') }}</span>
                        </p>
                        @if ($items->where('status', 'scheduled')->count() > 1 && $items->first()->isSkippable())
                            <form method="POST" action="{{ route('schedule.skip-day') }}"
                                  onsubmit="return confirm('Skip all meals on {{ $d->format('d M') }}? Their value will be credited to your wallet.')">
                                @csrf
                                <input type="hidden" name="date" value="{{ $date }}">
                                <button class="chip transition hover:border-coral hover:text-coral">Skip whole day</button>
                            </form>
                        @endif
                    </div>
                    <div class="grid gap-4 p-5 md:grid-cols-3">
                        @foreach ($items as $item)
                            <div class="cat-{{ $item->meal?->category ?? 'veg' }} flex items-center justify-between gap-3 rounded-2xl border border-ink-line bg-ink-soft p-4 {{ $item->status === 'skipped' ? 'opacity-45' : '' }}">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="text-3xl">{{ $item->meal?->emoji }}</span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] uppercase tracking-widest text-cream-dim">{{ ucfirst($item->meal_time) }} · ₹{{ number_format($item->unit_price) }}</p>
                                        <p class="truncate text-sm font-semibold {{ $item->status === 'skipped' ? 'line-through' : '' }}">{{ $item->meal?->name }}</p>
                                    </div>
                                </div>
                                @if ($item->status === 'scheduled' && $item->isSkippable())
                                    <form method="POST" action="{{ route('schedule.skip', $item) }}">
                                        @csrf
                                        <button class="rounded-full border border-ink-line px-3 py-1.5 text-xs font-semibold text-cream-dim transition hover:border-coral hover:text-coral" title="Skip & credit wallet">Skip</button>
                                    </form>
                                @elseif ($item->status === 'skipped' && now()->lt($item->date->copy()->startOfDay()->subHours((int) \App\Models\Setting::get('skip_cutoff_hours', 12))))
                                    <form method="POST" action="{{ route('schedule.unskip', $item) }}">
                                        @csrf
                                        <button class="rounded-full border border-ink-line px-3 py-1.5 text-xs font-semibold text-cream-dim transition hover:border-lime-neon hover:text-lime-neon">Undo</button>
                                    </form>
                                @else
                                    <span class="text-[10px] uppercase tracking-widest text-cream-dim/60">{{ $item->status === 'scheduled' ? 'Locked' : ucwords(str_replace('_', ' ', $item->status)) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</section>
@endsection
