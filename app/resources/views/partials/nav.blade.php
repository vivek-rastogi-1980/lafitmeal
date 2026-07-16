<header data-nav class="fixed inset-x-0 top-0 z-50 transition-all duration-500">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8" x-data="{ open: false }">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-lime-neon text-lg font-black text-ink transition-transform duration-500 group-hover:rotate-[360deg]">L</span>
            <span class="font-display text-sm font-bold uppercase tracking-widest">LaFit<span class="text-lime-neon">Meal</span></span>
        </a>

        {{-- Meal search --}}
        <div x-data="mealSearch()" class="relative mx-4 hidden flex-1 max-w-xs md:block lg:max-w-sm">
            <form role="search" action="{{ route('menu') }}" method="GET" @submit="close()">
                <label for="meal-search" class="sr-only">Search meals</label>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-cream-dim" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m20 20-3.5-3.5" />
                </svg>
                <input id="meal-search" name="q" type="search" autocomplete="off" placeholder="Search meals…"
                       x-model="q"
                       @input.debounce.250ms="lookup()"
                       @focus="if (results.length) showResults = true"
                       @keydown.arrow-down.prevent="move(1)"
                       @keydown.arrow-up.prevent="move(-1)"
                       @keydown.enter="go($event)"
                       @keydown.escape="close()"
                       class="w-full rounded-full border border-ink-line bg-ink-card/70 py-2.5 pl-10 pr-4 text-sm text-cream placeholder:text-cream-dim/70 backdrop-blur transition focus:border-lime-neon focus:outline-none focus:ring-1 focus:ring-lime-neon">
            </form>

            {{-- type-ahead results --}}
            <div x-show="showResults" x-cloak x-transition.opacity @click.outside="close()"
                 class="absolute inset-x-0 top-full mt-2 overflow-hidden rounded-2xl border border-ink-line bg-ink/95 shadow-2xl backdrop-blur-xl">
                <template x-if="loading">
                    <p class="px-4 py-3 text-sm text-cream-dim">Searching…</p>
                </template>
                <template x-if="!loading && results.length === 0">
                    <p class="px-4 py-3 text-sm text-cream-dim">No meals match “<span x-text="q"></span>”.</p>
                </template>
                <template x-for="(meal, i) in results" :key="meal.url">
                    <a :href="meal.url" @mouseenter="active = i"
                       :class="active === i ? 'bg-ink-card' : ''"
                       class="flex items-center gap-3 border-b border-ink-line/60 px-3 py-2.5 last:border-0">
                        <template x-if="meal.image">
                            <img :src="meal.image" alt="" class="h-11 w-11 shrink-0 rounded-lg object-cover">
                        </template>
                        <template x-if="!meal.image">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-ink-card text-xl" x-text="meal.emoji"></span>
                        </template>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-display text-[11px] font-bold uppercase tracking-wide text-cream" x-text="meal.name"></span>
                            <span class="mt-0.5 block text-[11px] text-cream-dim">
                                <span x-text="meal.categoryLabel"></span> ·
                                <span x-text="meal.calories"></span> kcal ·
                                <span x-text="meal.protein"></span>g protein
                            </span>
                        </span>
                    </a>
                </template>
                <a :href="'{{ route('menu') }}?q=' + encodeURIComponent(q)" x-show="results.length"
                   class="block bg-ink-card/60 px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-wider text-lime-neon">
                    See all results →
                </a>
            </div>
        </div>

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
            <form role="search" action="{{ route('menu') }}" method="GET" class="relative mb-5">
                <label for="meal-search-mobile" class="sr-only">Search meals</label>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-cream-dim" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m20 20-3.5-3.5" />
                </svg>
                <input id="meal-search-mobile" name="q" type="search" autocomplete="off" placeholder="Search meals…"
                       class="w-full rounded-full border border-ink-line bg-ink-card/70 py-3 pl-10 pr-4 text-base text-cream placeholder:text-cream-dim/70 focus:border-lime-neon focus:outline-none focus:ring-1 focus:ring-lime-neon">
            </form>
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
