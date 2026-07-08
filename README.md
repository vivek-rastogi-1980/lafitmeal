# LaFitMeal

Subscription-based healthy meal delivery platform — Laravel 13, Filament admin,
GSAP/Three.js animated frontend.

```
lafitmeal/
├── app/      ← the Laravel application (see app/DEPLOYMENT.md for Hostinger)
└── tools/    ← portable PHP 8.3 + Composer for local dev (not committed)
```

## Local development (Windows, no system PHP needed)

```powershell
cd app
..\tools\php\php.exe artisan serve          # http://127.0.0.1:8000
..\tools\php\php.exe artisan migrate --seed # SQLite + 135 meals + admin user
npm run dev                                  # Vite HMR (Node required)
..\tools\php\php.exe ..\tools\composer.phar <cmd>   # composer
```

- Admin panel: `/admin` — `admin@lafitmeal.com` / `ChangeMe@123`
- Payments: stubbed `offline` gateway (`PAYMENT_DRIVER`); implement
  `App\Services\Payments\PaymentGateway` for Razorpay/Stripe.
- Meal images: `tools/php/php.exe tools/import_meal_image.php <url> <slug>`
  saves a webp into `app/storage/app/public/meals/`.
