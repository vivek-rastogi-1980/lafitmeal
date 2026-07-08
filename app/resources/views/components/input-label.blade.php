@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-cream-dim']) }}>
    {{ $value ?? $slot }}
</label>
