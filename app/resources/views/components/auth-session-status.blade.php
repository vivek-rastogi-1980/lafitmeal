@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-lime-neon']) }}>
        {{ $status }}
    </div>
@endif
