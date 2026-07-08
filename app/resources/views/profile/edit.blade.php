@extends('layouts.site')

@section('title', 'Your Profile | LaFitMeal')

@section('content')
<section class="mx-auto max-w-4xl px-5 pb-24 pt-32 lg:px-8">
    <p class="chip">Your account</p>
    <h1 class="h-display mt-4 text-4xl md:text-5xl">Profile <span class="text-lime-neon">settings</span></h1>

    {{-- flash --}}
    @foreach (['profile-updated' => 'Profile saved.', 'avatar-updated' => 'New profile picture looks great!', 'address-added' => 'Address added.', 'address-removed' => 'Address removed.', 'password-updated' => 'Password changed.'] as $key => $msg)
        @if (session('status') === $key)
            <div class="glass mt-8 border-lime-neon/50 p-4 text-sm">✅ {{ $msg }}</div>
        @endif
    @endforeach

    {{-- ===== avatar + basics ===== --}}
    <div class="glass mt-10 p-8">
        <h2 class="font-display text-base font-bold uppercase tracking-wide">Photo & details</h2>

        <div class="mt-6 flex flex-wrap items-center gap-6">
            <img src="{{ $user->avatar_url }}" alt="" class="h-24 w-24 rounded-full border-2 border-ink-line object-cover">
            <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="avatar" accept="image/*" required
                       class="text-sm text-cream-dim file:mr-3 file:rounded-full file:border-0 file:bg-lime-neon file:px-5 file:py-2.5 file:text-sm file:font-semibold file:text-ink hover:file:shadow-[0_0_24px_rgba(198,242,78,.4)]">
                <button class="btn-ghost !px-5 !py-2.5 text-sm">Upload</button>
            </form>
            @error('avatar') <p class="w-full text-sm text-coral">{{ $message }}</p> @enderror
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
            @csrf @method('PATCH')
            <div><label class="text-sm">Name</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1.5 w-full"></div>
            <div><label class="text-sm">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1.5 w-full"></div>
            <div><label class="text-sm">Phone</label><input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1.5 w-full" placeholder="+91"></div>
            <div class="flex items-end"><button class="btn-primary !py-2.5">Save changes</button></div>
            @error('name') <p class="text-sm text-coral">{{ $message }}</p> @enderror
            @error('email') <p class="text-sm text-coral">{{ $message }}</p> @enderror
        </form>
    </div>

    {{-- ===== addresses ===== --}}
    <div class="glass mt-8 p-8" x-data="{ adding: {{ $user->addresses->isEmpty() ? 'true' : 'false' }} }">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-base font-bold uppercase tracking-wide">Delivery addresses</h2>
            <button @click="adding = !adding" class="chip hover:border-lime-neon hover:text-lime-neon" x-text="adding ? '× Cancel' : '+ Add address'"></button>
        </div>

        <div class="mt-6 space-y-3">
            @forelse ($user->addresses as $address)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-ink-line bg-ink-soft px-5 py-4">
                    <div class="text-sm">
                        <span class="font-bold">{{ $address->label }}</span>
                        @if ($address->is_default)<span class="ml-2 rounded-full bg-lime-neon/15 px-2.5 py-0.5 text-xs font-semibold text-lime-neon">Default</span>@endif
                        <p class="mt-1 text-cream-dim">{{ $address->full_address }}</p>
                    </div>
                    <form method="POST" action="{{ route('profile.address.destroy', $address) }}" onsubmit="return confirm('Remove this address?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-cream-dim transition hover:text-coral">Remove</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-cream-dim">No addresses yet — add one for deliveries.</p>
            @endforelse
        </div>

        <form x-show="adding" x-cloak method="POST" action="{{ route('profile.address.store') }}" class="mt-6 grid gap-4 border-t border-ink-line pt-6 sm:grid-cols-2">
            @csrf
            <div><label class="text-sm">Label</label>
                <select name="label" class="mt-1.5 w-full"><option>Home</option><option>Work</option><option>Other</option></select>
            </div>
            <div><label class="text-sm">Pincode</label><input type="text" name="pincode" required class="mt-1.5 w-full"></div>
            <div class="sm:col-span-2"><label class="text-sm">Address line 1</label><input type="text" name="line1" required class="mt-1.5 w-full"></div>
            <div class="sm:col-span-2"><label class="text-sm">Address line 2</label><input type="text" name="line2" class="mt-1.5 w-full"></div>
            <div><label class="text-sm">City</label><input type="text" name="city" required class="mt-1.5 w-full"></div>
            <div><label class="text-sm">State</label><input type="text" name="state" required class="mt-1.5 w-full"></div>
            <label class="flex items-center gap-2.5 text-sm sm:col-span-2">
                <input type="checkbox" name="is_default" value="1" class="rounded border-ink-line bg-ink text-lime-neon focus:ring-lime-neon"> Make this my default address
            </label>
            <div><button class="btn-primary !py-2.5">Save address</button></div>
        </form>
    </div>

    {{-- ===== password ===== --}}
    <div class="glass mt-8 p-8">
        <h2 class="font-display text-base font-bold uppercase tracking-wide">Change password</h2>
        <form method="POST" action="{{ route('password.update') }}" class="mt-6 grid gap-5 sm:grid-cols-3">
            @csrf @method('PUT')
            <div><label class="text-sm">Current password</label><input type="password" name="current_password" class="mt-1.5 w-full"></div>
            <div><label class="text-sm">New password</label><input type="password" name="password" class="mt-1.5 w-full"></div>
            <div><label class="text-sm">Confirm new</label><input type="password" name="password_confirmation" class="mt-1.5 w-full"></div>
            @if ($errors->updatePassword->any())
                <p class="text-sm text-coral sm:col-span-3">{{ $errors->updatePassword->first() }}</p>
            @endif
            <div><button class="btn-primary !py-2.5">Update password</button></div>
        </form>
    </div>

    {{-- ===== danger zone ===== --}}
    <div class="glass mt-8 border-coral/30 p-8" x-data="{ confirming: false }">
        <h2 class="font-display text-base font-bold uppercase tracking-wide text-coral">Danger zone</h2>
        <p class="mt-2 text-sm text-cream-dim">Deleting your account removes your plans, wallet and history permanently.</p>
        <button x-show="!confirming" @click="confirming = true" class="btn-ghost mt-5 !border-coral/50 !text-coral hover:!border-coral">Delete account</button>
        <form x-show="confirming" x-cloak method="POST" action="{{ route('profile.destroy') }}" class="mt-5 flex flex-wrap items-end gap-4">
            @csrf @method('DELETE')
            <div><label class="text-sm">Confirm with your password</label><input type="password" name="password" required class="mt-1.5 w-full"></div>
            <button class="rounded-full bg-coral px-6 py-3 font-semibold text-ink">Yes, delete forever</button>
            <button type="button" @click="confirming = false" class="btn-ghost !py-3">Cancel</button>
            @if ($errors->userDeletion->any())
                <p class="w-full text-sm text-coral">{{ $errors->userDeletion->first() }}</p>
            @endif
        </form>
    </div>
</section>
@endsection
