@extends('layouts.site')

@section('title', 'Build Your Plan | LaFitMeal')

@section('content')
<section class="mx-auto max-w-6xl px-5 pb-24 pt-32 lg:px-8"
         x-data="planBuilder()" x-init="init()">

    <p class="chip">Plan builder</p>
    <h1 class="h-display mt-4 text-4xl md:text-6xl">Design your <span class="text-lime-neon">week</span></h1>

    {{-- stepper --}}
    <div class="mt-10 flex items-center gap-3">
        <template x-for="s in 4" :key="s">
            <button @click="if (s < step) step = s"
                    class="h-2.5 rounded-full transition-all duration-500"
                    :class="step >= s ? 'w-14 bg-lime-neon' : 'w-8 bg-ink-line'"></button>
        </template>
        <span class="ml-2 text-xs uppercase tracking-widest text-cream-dim" x-text="['', 'Your kitchen', 'Your week', 'Duration', 'Delivery & pay'][step]"></span>
    </div>

    @if ($errors->any())
        <div class="glass mt-6 border-coral/50 p-4 text-sm text-coral">{{ $errors->first() }}</div>
    @endif

    {{-- ============ STEP 1 : CATEGORY ============ --}}
    <div x-show="step === 1" x-transition.opacity.duration.400ms class="mt-12">
        <h2 class="font-display text-xl font-bold uppercase">Pick your kitchen</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <template x-for="cat in categories" :key="cat.slug">
                <button @click="category = cat.slug; applyCategory()"
                        class="group rounded-3xl border p-8 text-left transition-all duration-300"
                        :class="category === cat.slug ? 'border-lime-neon bg-ink-card shadow-[0_0_50px_rgba(198,242,78,.15)]' : 'border-ink-line bg-ink-soft hover:border-cream-dim'">
                    <span class="block text-5xl transition-transform duration-500 group-hover:scale-125" x-text="cat.emoji"></span>
                    <span class="mt-5 block font-display text-lg font-bold uppercase" x-text="cat.label"></span>
                    <span class="mt-2 block text-sm text-cream-dim" x-text="cat.desc"></span>
                    <span class="mt-4 inline-block rounded-full px-3 py-1 text-xs font-bold"
                          :class="category === cat.slug ? 'bg-lime-neon text-ink' : 'bg-ink-line text-cream-dim'"
                          x-text="category === cat.slug ? '✓ Selected' : 'Select'"></span>
                </button>
            </template>
        </div>
        <div class="mt-10 flex justify-end">
            <button @click="step = 2" class="btn-primary">Next: design the week →</button>
        </div>
    </div>

    {{-- ============ STEP 2 : WEEKLY GRID ============ --}}
    <div x-show="step === 2" x-transition.opacity.duration.400ms x-cloak class="mt-12">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-display text-xl font-bold uppercase">Toggle your meals, day by day</h2>
            <div class="flex gap-2">
                <button @click="setAll(true)" class="chip hover:border-lime-neon hover:text-lime-neon">Enable all</button>
                <button @click="setAll(false)" class="chip hover:border-coral hover:text-coral">Clear all</button>
            </div>
        </div>
        <p class="mt-2 text-sm text-cream-dim">Tap a cell to enable/disable it. Use the dropdown inside an enabled cell to swap the dish.</p>

        <div class="mt-8 overflow-x-auto">
            <div class="min-w-[760px]">
                {{-- header --}}
                <div class="grid grid-cols-[110px_repeat(3,1fr)] gap-3">
                    <div></div>
                    <template x-for="mt in mealTimes" :key="mt.slug">
                        <div class="text-center">
                            <span class="chip" x-text="mt.icon + ' ' + mt.label"></span>
                        </div>
                    </template>
                </div>
                {{-- rows --}}
                <template x-for="(day, d) in days" :key="d">
                    <div class="mt-3 grid grid-cols-[110px_repeat(3,1fr)] items-stretch gap-3">
                        <div class="flex items-center font-display text-sm font-bold uppercase text-cream-dim" x-text="day"></div>
                        <template x-for="mt in mealTimes" :key="mt.slug">
                            <div class="rounded-2xl border p-3 transition-all duration-300"
                                 :class="week[d][mt.slug].enabled ? 'border-lime-neon/60 bg-ink-card' : 'border-ink-line bg-ink-soft opacity-50'">
                                <div class="flex items-center justify-between gap-2">
                                    <button @click="week[d][mt.slug].enabled = !week[d][mt.slug].enabled; refreshQuote()"
                                            class="relative h-6 w-11 shrink-0 rounded-full transition-colors duration-300"
                                            :class="week[d][mt.slug].enabled ? 'bg-lime-neon' : 'bg-ink-line'">
                                        <span class="absolute top-0.5 h-5 w-5 rounded-full bg-ink transition-all duration-300"
                                              :class="week[d][mt.slug].enabled ? 'left-[22px]' : 'left-0.5'"></span>
                                    </button>
                                    <span class="text-xs font-bold text-lime-neon" x-show="week[d][mt.slug].enabled"
                                          x-text="'₹' + mealPrice(week[d][mt.slug].meal_id)"></span>
                                </div>
                                <select x-show="week[d][mt.slug].enabled" x-cloak
                                        x-model.number="week[d][mt.slug].meal_id" @change="refreshQuote()"
                                        class="mt-2.5 w-full !rounded-lg !border-ink-line !bg-ink !py-1.5 !text-xs">
                                    <template x-for="m in mealsFor(mt.slug)" :key="m.id">
                                        <option :value="m.id" x-text="m.name + ' · ₹' + Math.round(m.price)"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-between gap-4">
            <button @click="step = 1" class="btn-ghost">← Back</button>
            <div class="flex items-center gap-5">
                <span class="text-sm text-cream-dim"><b class="text-cream" x-text="enabledCount()"></b> meals / week</span>
                <button @click="step = 3" :disabled="enabledCount() === 0"
                        class="btn-primary disabled:cursor-not-allowed disabled:opacity-40">Next: duration →</button>
            </div>
        </div>
    </div>

    {{-- ============ STEP 3 : DURATION ============ --}}
    <div x-show="step === 3" x-transition.opacity.duration.400ms x-cloak class="mt-12">
        <h2 class="font-display text-xl font-bold uppercase">How long are we going?</h2>
        <p class="mt-2 text-sm text-cream-dim">Your weekly design repeats for the whole plan. Minimum 15 days.</p>

        <div class="mt-8 grid gap-5 sm:grid-cols-3">
            <template x-for="opt in durations" :key="opt.days">
                <button @click="duration = opt.days; refreshQuote()"
                        class="rounded-3xl border p-7 text-left transition-all duration-300"
                        :class="duration === opt.days ? 'border-lime-neon bg-ink-card shadow-[0_0_50px_rgba(198,242,78,.15)]' : 'border-ink-line bg-ink-soft hover:border-cream-dim'">
                    <span class="font-display text-4xl font-black" x-text="opt.days"></span>
                    <span class="ml-1 text-sm text-cream-dim">days</span>
                    <p class="mt-2 text-sm font-semibold" :class="duration === opt.days ? 'text-lime-neon' : 'text-cream-dim'" x-text="opt.tag"></p>
                </button>
            </template>
        </div>

        <div class="mt-8 max-w-xs">
            <label class="text-sm">Start date</label>
            <input type="date" x-model="startDate" @change="refreshQuote()" :min="minStart" class="mt-2 w-full">
        </div>

        <div class="mt-10 flex justify-between">
            <button @click="step = 2" class="btn-ghost">← Back</button>
            <button @click="step = 4; refreshQuote()" class="btn-primary">Next: delivery →</button>
        </div>
    </div>

    {{-- ============ STEP 4 : DELIVERY + REVIEW ============ --}}
    <div x-show="step === 4" x-transition.opacity.duration.400ms x-cloak class="mt-12 grid gap-10 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <h2 class="font-display text-xl font-bold uppercase">Delivery details</h2>

            @if ($addresses->isNotEmpty())
                <div class="mt-6 space-y-3">
                    @foreach ($addresses as $addr)
                        <label class="glass flex cursor-pointer items-center gap-4 p-4 transition hover:border-lime-neon">
                            <input type="radio" name="addr" value="{{ $addr->id }}" x-model.number="addressId"
                                   class="!h-5 !w-5 !rounded-full border-ink-line bg-ink text-lime-neon focus:ring-lime-neon">
                            <span class="text-sm"><b>{{ $addr->label }}</b> — {{ $addr->full_address }}</span>
                        </label>
                    @endforeach
                    <label class="glass flex cursor-pointer items-center gap-4 p-4 transition hover:border-lime-neon">
                        <input type="radio" name="addr" value="" x-model="addressId"
                               class="!h-5 !w-5 !rounded-full border-ink-line bg-ink text-lime-neon focus:ring-lime-neon">
                        <span class="text-sm font-semibold">+ Use a new address</span>
                    </label>
                </div>
            @endif

            <div x-show="!addressId" class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2"><label class="text-sm">Address line 1</label><input type="text" x-model="address.line1" class="mt-1.5 w-full" placeholder="Flat / house, street"></div>
                <div class="sm:col-span-2"><label class="text-sm">Address line 2 (optional)</label><input type="text" x-model="address.line2" class="mt-1.5 w-full" placeholder="Landmark, area"></div>
                <div><label class="text-sm">City</label><input type="text" x-model="address.city" class="mt-1.5 w-full"></div>
                <div><label class="text-sm">State</label><input type="text" x-model="address.state" class="mt-1.5 w-full"></div>
                <div><label class="text-sm">Pincode</label><input type="text" x-model="address.pincode" class="mt-1.5 w-full"></div>
                <div><label class="text-sm">Phone</label><input type="tel" x-model="phone" class="mt-1.5 w-full" placeholder="For delivery updates"></div>
            </div>

            <div class="mt-8 max-w-xs">
                <label class="text-sm">Coupon code (optional)</label>
                <input type="text" x-model="coupon" @change="refreshQuote()" class="mt-1.5 w-full uppercase" placeholder="WELCOME">
            </div>
        </div>

        {{-- live quote --}}
        <aside class="lg:col-span-2">
            <div class="glass sticky top-28 p-7">
                <h3 class="font-display text-base font-bold uppercase tracking-wide">Your plan</h3>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-cream-dim">Kitchen</dt><dd class="font-semibold uppercase" x-text="category.replace('_',' ')"></dd></div>
                    <div class="flex justify-between"><dt class="text-cream-dim">Duration</dt><dd class="font-semibold" x-text="duration + ' days'"></dd></div>
                    <div class="flex justify-between"><dt class="text-cream-dim">Starts</dt><dd class="font-semibold" x-text="startDate"></dd></div>
                    <div class="flex justify-between"><dt class="text-cream-dim">Total meals</dt><dd class="font-semibold" x-text="quote.meal_count ?? '—'"></dd></div>
                    <div class="my-3 border-t border-ink-line"></div>
                    <div class="flex justify-between"><dt class="text-cream-dim">Subtotal</dt><dd x-text="money(quote.subtotal)"></dd></div>
                    <div class="flex justify-between" x-show="quote.discount > 0"><dt class="text-cream-dim">Coupon</dt><dd class="text-mint" x-text="'− ' + money(quote.discount)"></dd></div>
                    <div class="flex justify-between" x-show="walletBalance > 0"><dt class="text-cream-dim">Wallet credit</dt><dd class="text-mint" x-text="'− ' + money(Math.min(walletBalance, quote.total ?? 0))"></dd></div>
                </dl>
                <div class="mt-5 flex items-end justify-between border-t border-ink-line pt-5">
                    <span class="text-sm uppercase tracking-widest text-cream-dim">To pay</span>
                    <span class="font-display text-3xl font-black text-lime-neon" x-text="money(Math.max((quote.total ?? 0) - walletBalance, 0))"></span>
                </div>
                <button @click="submit()" :disabled="submitting"
                        class="btn-primary mt-7 w-full justify-center disabled:opacity-50">
                    <span x-show="!submitting">Confirm & start plan 🚀</span>
                    <span x-show="submitting">Creating your plan…</span>
                </button>
                <p class="mt-4 text-center text-xs text-cream-dim">Skip any meal before cutoff — its value returns to your wallet.</p>
                <button @click="step = 3" class="mt-3 w-full text-center text-sm text-cream-dim hover:text-lime-neon">← Back</button>
            </div>
        </aside>
    </div>

</section>

<script>
function planBuilder() {
    return {
        step: 1,
        categories: [
            { slug: 'veg', label: 'Veg', emoji: '🥬', desc: 'Paneer, dals, millets — vegetarian power.' },
            { slug: 'non_veg', label: 'Non-Veg', emoji: '🍗', desc: 'Chicken, fish, eggs — lean & serious.' },
            { slug: 'vegan', label: 'Vegan', emoji: '🌱', desc: 'Tofu, legumes, grains — pure plants.' },
        ],
        mealTimes: [
            { slug: 'breakfast', label: 'Breakfast', icon: '☀️' },
            { slug: 'lunch', label: 'Lunch', icon: '🌤️' },
            { slug: 'dinner', label: 'Dinner', icon: '🌙' },
        ],
        days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        durations: [
            { days: 15, tag: 'The starter' },
            { days: 30, tag: 'Most popular' },
            { days: 60, tag: 'The transformation' },
        ],
        mealsByGroup: @json($mealsByGroup),
        walletBalance: {{ $walletBalance }},
        category: @json($defaultCategory),
        week: {},
        duration: 15,
        startDate: '',
        minStart: '',
        coupon: '',
        addressId: {{ $addresses->firstWhere('is_default', true)?->id ?? ($addresses->first()->id ?? 'null') }},
        address: { line1: '', line2: '', city: '', state: '', pincode: '' },
        phone: @json(auth()->user()->phone),
        quote: {},
        submitting: false,
        _quoteTimer: null,

        init() {
            const t = new Date(Date.now() + 86400000 * 2);
            this.minStart = this.startDate = t.toISOString().slice(0, 10);
            this.applyCategory();
        },

        mealsFor(mealTime) {
            return this.mealsByGroup[this.category + '.' + mealTime] ?? [];
        },

        applyCategory() {
            // (re)build the weekly grid with the first dish of each group preselected
            for (let d = 0; d < 7; d++) {
                if (!this.week[d]) this.week[d] = {};
                for (const mt of this.mealTimes) {
                    const first = this.mealsFor(mt.slug)[0];
                    this.week[d][mt.slug] = {
                        enabled: this.week[d][mt.slug]?.enabled ?? true,
                        meal_id: first ? first.id : null,
                    };
                }
            }
            this.refreshQuote();
        },

        setAll(state) {
            for (let d = 0; d < 7; d++)
                for (const mt of this.mealTimes)
                    this.week[d][mt.slug].enabled = state;
            this.refreshQuote();
        },

        enabledCount() {
            let n = 0;
            for (let d = 0; d < 7; d++)
                for (const mt of this.mealTimes)
                    if (this.week[d][mt.slug].enabled) n++;
            return n;
        },

        mealPrice(id) {
            for (const key in this.mealsByGroup) {
                const found = this.mealsByGroup[key].find((m) => m.id === id);
                if (found) return Math.round(found.price);
            }
            return 0;
        },

        slots() {
            const out = [];
            for (let d = 0; d < 7; d++)
                for (const mt of this.mealTimes)
                    out.push({
                        day_of_week: d,
                        meal_time: mt.slug,
                        meal_id: this.week[d][mt.slug].meal_id,
                        enabled: this.week[d][mt.slug].enabled,
                    });
            return out;
        },

        money(v) {
            return '₹' + (v ?? 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
        },

        refreshQuote() {
            clearTimeout(this._quoteTimer);
            this._quoteTimer = setTimeout(async () => {
                const res = await fetch(@json(route('plan.quote')), {
                    method: 'POST',
                    headers: this.headers(),
                    body: JSON.stringify({
                        slots: this.slots(),
                        start_date: this.startDate,
                        duration_days: this.duration,
                        coupon_code: this.coupon || null,
                    }),
                });
                if (res.ok) this.quote = await res.json();
            }, 350);
        },

        async submit() {
            this.submitting = true;
            const payload = {
                slots: this.slots(),
                start_date: this.startDate,
                duration_days: this.duration,
                coupon_code: this.coupon || null,
                meal_category: this.category,
                address_id: this.addressId || null,
                phone: this.phone || null,
            };
            if (!this.addressId) payload.address = this.address;

            const res = await fetch(@json(route('plan.store')), {
                method: 'POST',
                headers: this.headers(),
                body: JSON.stringify(payload),
                redirect: 'follow',
            });

            if (res.redirected) { window.location = res.url; return; }
            if (res.ok) { window.location = @json(route('dashboard')); return; }

            const err = await res.json().catch(() => ({}));
            alert(err.message ?? 'Something needs fixing — please check the form.');
            this.submitting = false;
        },

        headers() {
            return {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            };
        },
    };
}
</script>
@endsection
