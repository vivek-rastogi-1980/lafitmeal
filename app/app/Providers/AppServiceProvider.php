<?php

namespace App\Providers;

use App\Services\Payments\OfflineGateway;
use App\Services\Payments\PaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Swap this binding when a real gateway (Razorpay/Stripe) is chosen.
        $this->app->bind(PaymentGateway::class, match (config('services.payment.driver', 'offline')) {
            default => OfflineGateway::class,
        });
    }

    public function boot(): void
    {
        //
    }
}
