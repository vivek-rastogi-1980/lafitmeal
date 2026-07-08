@extends('layouts.site')

@section('title', 'Your Wallet | LaFitMeal')

@section('content')
<section class="mx-auto max-w-4xl px-5 pb-24 pt-32 lg:px-8">
    <p class="chip">Your money, kept safe</p>
    <h1 class="h-display mt-4 text-4xl md:text-5xl">The <span class="text-lime-neon">wallet</span></h1>

    <div data-reveal="zoom" class="glass relative mt-10 overflow-hidden p-10 text-center">
        <span class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-lime-neon/10 blur-3xl"></span>
        <p class="text-xs uppercase tracking-[0.3em] text-cream-dim">Available balance</p>
        <p class="mt-3 font-display text-6xl font-black text-lime-neon">₹<span data-count="{{ $wallet->balance }}">0</span></p>
        <p class="mx-auto mt-4 max-w-sm text-sm text-cream-dim">Credits from skipped meals live here and auto-apply to your next plan or renewal.</p>
    </div>

    <h2 class="h-display mt-14 text-2xl">History</h2>
    <div class="mt-6 space-y-3">
        @forelse ($transactions as $tx)
            <div data-reveal class="glass flex items-center justify-between gap-4 px-6 py-4">
                <div class="flex items-center gap-4">
                    <span class="grid h-11 w-11 place-items-center rounded-full {{ $tx->type === 'credit' ? 'bg-mint/15 text-mint' : 'bg-coral/15 text-coral' }} text-lg">
                        {{ $tx->type === 'credit' ? '↓' : '↑' }}
                    </span>
                    <div>
                        <p class="text-sm font-semibold">{{ $tx->description }}</p>
                        <p class="mt-0.5 text-xs text-cream-dim">{{ $tx->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-display font-bold {{ $tx->type === 'credit' ? 'text-mint' : 'text-coral' }}">
                        {{ $tx->type === 'credit' ? '+' : '−' }}₹{{ number_format($tx->amount, 2) }}
                    </p>
                    <p class="text-xs text-cream-dim">bal ₹{{ number_format($tx->balance_after, 2) }}</p>
                </div>
            </div>
        @empty
            <div class="glass p-12 text-center text-cream-dim">
                <span class="text-4xl">🪙</span>
                <p class="mt-4">No transactions yet. Skip a meal someday and watch the credits roll in.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $transactions->links() }}</div>
</section>
@endsection
