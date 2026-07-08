<x-guest-layout>
    <h1 class="font-display text-xl font-bold uppercase tracking-wide text-cream">Create your account</h1>
    <p class="mt-1.5 text-sm text-cream-dim">Two minutes to better eating.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Meal type -->
        <div class="mt-4">
            <x-input-label :value="__('How do you eat?')" />
            <div class="mt-2 grid grid-cols-3 gap-2">
                @foreach (['veg' => '🥬 Veg', 'non_veg' => '🍗 Non-Veg', 'vegan' => '🌱 Vegan'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="meal_category" value="{{ $value }}" class="peer sr-only"
                               @checked(old('meal_category', 'veg') === $value)>
                        <span class="block rounded-xl border border-ink-line bg-ink-soft px-2 py-3 text-center text-sm font-semibold text-cream-dim transition-all duration-300
                                     peer-checked:border-lime-neon peer-checked:bg-lime-neon/10 peer-checked:text-lime-neon">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <p class="mt-1.5 text-xs text-cream-dim/70">You can change this any time while building a plan.</p>
            <x-input-error :messages="$errors->get('meal_category')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-cream-dim hover:text-cream rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-neon" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
