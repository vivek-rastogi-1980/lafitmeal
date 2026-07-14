<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <title>{{ config('app.name', 'LaFitMeal') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="grain">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-5 py-10">
            {{-- ambient glows --}}
            <span class="pointer-events-none absolute -left-32 top-1/4 h-96 w-96 rounded-full bg-lime-neon/10 blur-3xl"></span>
            <span class="pointer-events-none absolute -right-32 bottom-1/4 h-96 w-96 rounded-full bg-mint/10 blur-3xl"></span>
            <span class="pointer-events-none absolute left-[12%] top-[16%] animate-float text-4xl opacity-60" style="animation-delay:-2s">🥦</span>
            <span class="pointer-events-none absolute right-[14%] top-[22%] animate-float text-5xl opacity-60" style="animation-delay:-4s">🍅</span>
            <span class="pointer-events-none absolute bottom-[18%] left-[18%] animate-float text-4xl opacity-50" style="animation-delay:-1s">🌶️</span>
            <span class="pointer-events-none absolute bottom-[24%] right-[16%] animate-float text-4xl opacity-50" style="animation-delay:-5s">🥑</span>

            <a href="{{ route('home') }}" class="group relative z-10 flex items-center gap-2.5">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-lime-neon text-xl font-black text-ink transition-transform duration-500 group-hover:rotate-[360deg]">L</span>
                <span class="font-display text-base font-bold uppercase tracking-widest text-cream">LaFit<span class="text-lime-neon">Meal</span></span>
            </a>

            <div class="glass relative z-10 mt-8 w-full max-w-md p-8">
                {{ $slot }}
            </div>

            <p class="relative z-10 mt-6 text-center font-accent text-lg italic text-cream-dim">eat bold, live light</p>
        </div>
    </body>
</html>
