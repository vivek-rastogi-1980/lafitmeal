<header data-nav class="fixed inset-x-0 top-0 z-50 transition-all duration-500">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8" x-data="{ open: false }">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-lime-neon text-lg font-black text-ink transition-transform duration-500 group-hover:rotate-[360deg]">L</span>
            <span class="font-display text-sm font-bold uppercase tracking-widest">LaFit<span class="text-lime-neon">Meal</span></span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-8 text-sm font-medium text-cream-dim md:flex">
            <a href="{{ route('menu') }}" class="transition hover:text-lime-neon">Menu</a>
            <a href="{{ route('how-it-works') }}" class="transition hover:text-lime-neon">How it works</a>
            @auth
                <a href="{{ route('dashboard') }}" class="transition hover:text-lime-neon">Dashboard</a>
                <a href="{{ route('wallet') }}" class="transition hover:text-lime-neon">Wallet</a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 transition hover:text-lime-neon">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-8 w-8 rounded-full border border-ink-line object-cover">
                </a>
            @else
                <a href="{{ route('login') }}" class="transition hover:text-lime-neon">Log in</a>
                <a href="{{ route('register') }}" data-magnet class="btn-primary !px-5 !py-2.5 text-sm">Start eating better</a>
            @endauth
        </div>

        {{-- Mobile toggle --}}
        <button class="md:hidden" @click="open = !open" aria-label="Menu">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>

        {{-- Mobile panel --}}
        <div x-show="open" x-cloak x-transition.opacity
             class="absolute inset-x-0 top-full border-b border-ink-line bg-ink/95 px-6 py-6 backdrop-blur-xl md:hidden">
            <div class="flex flex-col gap-4 text-lg">
                <a href="{{ route('menu') }}">Menu</a>
                <a href="{{ route('how-it-works') }}">How it works</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('wallet') }}">Wallet</a>
                    <a href="{{ route('profile.edit') }}">Profile</a>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary justify-center">Start eating better</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
