<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LaFitMeal — Healthy Meals, Delivered on Your Schedule')</title>
    <meta name="description" content="@yield('meta_description', 'Subscription-based healthy meal delivery. 135+ chef-crafted Veg, Non-Veg & Vegan meals with full macros. Build your weekly plan, skip any day, pay only for what you eat.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grain min-h-screen">

    @include('partials.nav')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

</body>
</html>
