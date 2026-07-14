@props(['meal'])

<a href="{{ route('meals.show', $meal) }}"
   class="tilt cat-{{ $meal->category }} cat-glow group relative block overflow-hidden rounded-3xl border border-ink-line bg-ink-card transition-shadow duration-500">

    {{-- image / animated placeholder --}}
    <div class="relative aspect-[4/3] overflow-hidden">
        @if ($meal->video_url)
            <video data-food-video poster="{{ $meal->image_url }}"
                   autoplay loop muted playsinline preload="metadata"
                   aria-label="{{ $meal->name }}"
                   class="h-full w-full object-cover transition duration-500 group-hover:brightness-110">
                <source src="{{ $meal->video_url }}" type="video/mp4">
            </video>
        @elseif ($meal->image_url)
            <img src="{{ $meal->image_url }}" alt="{{ $meal->name }}" loading="lazy"
                 class="food-photo h-full w-full object-cover transition duration-500 group-hover:brightness-110">
        @else
            <div class="grid h-full w-full place-items-center"
                 style="background: radial-gradient(circle at 30% 20%, rgb(var(--cat) / .28), transparent 55%), radial-gradient(circle at 75% 80%, rgb(var(--cat) / .14), #101a13 60%);">
                <span class="tilt-pop text-7xl drop-shadow-[0_18px_28px_rgb(var(--cat)/.35)] transition-transform duration-500 group-hover:scale-125 group-hover:-rotate-6">{{ $meal->emoji }}</span>
            </div>
        @endif

        {{-- badges --}}
        <div class="absolute left-3 top-3 flex gap-2">
            <span class="cat-badge rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider">{{ $meal->category_label }}</span>
            @if ($meal->badge)
                <span class="rounded-full bg-ink/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-cream backdrop-blur">{{ $meal->badge }}</span>
            @endif
        </div>
        <span class="absolute right-3 top-3 rounded-full bg-ink/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-cream-dim backdrop-blur">{{ ucfirst($meal->meal_time) }}</span>
    </div>

    {{-- body --}}
    <div class="p-5">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-display text-sm font-bold uppercase leading-snug tracking-wide">{{ $meal->name }}</h3>
            <span class="cat-text whitespace-nowrap font-display text-sm font-bold">₹{{ number_format($meal->price) }}</span>
        </div>
        <p class="mt-2 line-clamp-1 text-sm text-cream-dim">{{ $meal->tagline }}</p>

        {{-- macros --}}
        <div class="mt-4 grid grid-cols-4 gap-2 border-t border-ink-line pt-4 text-center">
            @foreach ([['kcal', $meal->calories], ['protein', $meal->protein_g . 'g'], ['carbs', $meal->carbs_g . 'g'], ['fat', $meal->fat_g . 'g']] as [$label, $value])
                <div>
                    <p class="text-sm font-bold">{{ $value }}</p>
                    <p class="text-[10px] uppercase tracking-widest text-cream-dim/70">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</a>
