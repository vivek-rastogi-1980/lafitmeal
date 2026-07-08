@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-ink-soft border-ink-line text-cream rounded-xl focus:border-lime-neon focus:ring-lime-neon/40 w-full']) }}>
