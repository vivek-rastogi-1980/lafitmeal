<?php

namespace App\Services\Payments;

use App\Models\Payment;

/**
 * Contract for payment gateways. Swap the binding in AppServiceProvider
 * (offline stub today; Razorpay/Stripe implementations later) without
 * touching checkout code.
 */
interface PaymentGateway
{
    /**
     * Initiate a payment for the given pending Payment record.
     * Returns data the frontend needs (e.g. gateway order id, checkout URL/key).
     */
    public function initiate(Payment $payment): array;

    /**
     * Verify/capture a payment after the gateway callback.
     * Returns true when the payment is confirmed paid.
     */
    public function confirm(Payment $payment, array $payload): bool;
}
