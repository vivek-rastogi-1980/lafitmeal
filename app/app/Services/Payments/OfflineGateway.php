<?php

namespace App\Services\Payments;

use App\Models\Payment;

/**
 * Stub gateway used until a real provider (Razorpay/Stripe) is chosen.
 * Marks payments as paid immediately so the full subscription flow can
 * be exercised end-to-end. Replace via the PaymentGateway binding.
 */
class OfflineGateway implements PaymentGateway
{
    public function initiate(Payment $payment): array
    {
        $payment->update(['gateway_ref' => 'OFFLINE-' . strtoupper(uniqid())]);

        return [
            'gateway' => 'offline',
            'reference' => $payment->gateway_ref,
            'auto_confirm' => true,
        ];
    }

    public function confirm(Payment $payment, array $payload): bool
    {
        $payment->update(['status' => 'paid']);

        return true;
    }
}
