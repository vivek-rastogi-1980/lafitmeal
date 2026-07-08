<footer class="relative mt-28 border-t border-ink-line">
    <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
        <div class="grid gap-12 md:grid-cols-4">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-lime-neon text-lg font-black text-ink">L</span>
                    <span class="font-display text-sm font-bold uppercase tracking-widest">LaFit<span class="text-lime-neon">Meal</span></span>
                </a>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-cream-dim">
                    Chef-crafted, macro-counted meals delivered on your schedule.
                    Skip any day — your wallet keeps the balance.
                </p>
            </div>
            <div>
                <p class="font-display text-xs font-bold uppercase tracking-widest text-cream-dim">Explore</p>
                <div class="mt-4 flex flex-col gap-2.5 text-sm text-cream-dim">
                    <a href="{{ route('menu', ['category' => 'veg']) }}" class="hover:text-lime-neon">Veg menu</a>
                    <a href="{{ route('menu', ['category' => 'non_veg']) }}" class="hover:text-coral">Non-veg menu</a>
                    <a href="{{ route('menu', ['category' => 'vegan']) }}" class="hover:text-mint">Vegan menu</a>
                    <a href="{{ route('how-it-works') }}" class="hover:text-lime-neon">How it works</a>
                </div>
            </div>
            <div>
                <p class="font-display text-xs font-bold uppercase tracking-widest text-cream-dim">Account</p>
                <div class="mt-4 flex flex-col gap-2.5 text-sm text-cream-dim">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-lime-neon">Dashboard</a>
                        <a href="{{ route('wallet') }}" class="hover:text-lime-neon">Wallet</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="hover:text-lime-neon">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-lime-neon">Log in</a>
                        <a href="{{ route('register') }}" class="hover:text-lime-neon">Sign up</a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-ink-line pt-7 text-xs text-cream-dim/60 md:flex-row">
            <p>© {{ date('Y') }} LaFitMeal. Eat bold, live light.</p>
            <p class="font-accent italic">made with real ingredients & unreal design</p>
        </div>
    </div>
</footer>
