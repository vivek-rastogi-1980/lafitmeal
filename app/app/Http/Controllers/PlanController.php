<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Payments\PaymentGateway;
use App\Services\PricingService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function create(Request $request)
    {
        $meals = Meal::active()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'category', 'meal_time', 'price', 'calories', 'protein_g', 'image_path', 'badge'])
            ->groupBy(fn ($m) => $m->category . '.' . $m->meal_time);

        return view('plan.create', [
            'mealsByGroup' => $meals,
            'defaultCategory' => $request->user()->meal_category ?? 'veg',
            'addresses' => $request->user()->addresses,
            'walletBalance' => (float) ($request->user()->wallet->balance ?? 0),
        ]);
    }

    public function quote(Request $request, PricingService $pricing)
    {
        $data = $this->validated($request);

        return response()->json($pricing->quote(
            $data['slots'],
            $data['start_date'],
            (int) $data['duration_days'],
            $data['coupon_code'] ?? null
        ));
    }

    public function store(Request $request, SubscriptionService $service, PaymentGateway $gateway)
    {
        $data = $this->validated($request, full: true);

        if ($request->user()->activeSubscription) {
            return back()->withErrors(['plan' => 'You already have an active plan. Manage it from your dashboard.']);
        }

        // Persist the user's category preference and address
        $request->user()->update(['meal_category' => $data['meal_category']]);
        if (! empty($data['address'])) {
            $address = $request->user()->addresses()->create($data['address'] + ['is_default' => true]);
            $data['address_id'] = $address->id;
        }

        $result = $service->create($request->user(), $data);

        // Offline stub gateway: confirm & activate immediately.
        // Real gateways will redirect to checkout and confirm via webhook/callback.
        if (($result['gatewayData']['auto_confirm'] ?? false) === true) {
            $gateway->confirm($result['payment'], []);
            $service->activate($result['subscription'], $result['payment']);

            return redirect()->route('dashboard')->with('status', 'plan-activated');
        }

        return redirect()->route('dashboard')->with('status', 'payment-pending');
    }

    protected function validated(Request $request, bool $full = false): array
    {
        $rules = [
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'slots.*.meal_time' => ['required', Rule::in(Meal::MEAL_TIMES)],
            'slots.*.meal_id' => ['nullable', 'integer', 'exists:meals,id'],
            'slots.*.enabled' => ['required', 'boolean'],
            'start_date' => ['required', 'date', 'after:today'],
            'duration_days' => ['required', 'integer', 'min:' . Subscription::MIN_DURATION_DAYS, 'max:90'],
            'coupon_code' => ['nullable', 'string', 'max:30'],
        ];

        if ($full) {
            $rules += [
                'meal_category' => ['required', Rule::in(Meal::CATEGORIES)],
                'address_id' => ['nullable', 'integer', Rule::exists('addresses', 'id')->where('user_id', $request->user()->id)],
                'address' => ['required_without:address_id', 'nullable', 'array'],
                'address.line1' => ['required_with:address', 'string', 'max:190'],
                'address.line2' => ['nullable', 'string', 'max:190'],
                'address.city' => ['required_with:address', 'string', 'max:100'],
                'address.state' => ['required_with:address', 'string', 'max:100'],
                'address.pincode' => ['required_with:address', 'string', 'max:10'],
                'phone' => ['nullable', 'string', 'max:20'],
            ];
        }

        $data = $request->validate($rules);

        if (! empty($data['phone'])) {
            $request->user()->update(['phone' => $data['phone']]);
        }

        return $data;
    }
}
