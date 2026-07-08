# Deploying LaFitMeal to Hostinger (Shared / Business hosting)

The app is built to run fully on shared hosting: database queue (no daemon),
one cron entry, no Node needed on the server (assets are pre-built).

## 0. Before you upload

On your PC, from the `app/` folder:

```
# build production assets (they land in public/build)
npm run build

# make sure dev-only stuff isn't in the upload
# (vendor/ CAN be uploaded — see step 3 note)
```

Create the zip: everything in `app/` **except** `node_modules/`, `.git/`,
`tests/`, `database/database.sqlite`.

## 1. hPanel basics

1. **PHP version** → hPanel → Advanced → PHP Configuration → select **PHP 8.3**.
   Enable extensions: `intl`, `zip` (most are on by default).
2. **Database** → hPanel → Databases → MySQL → create a database + user, note
   the name/user/password (they are prefixed with your account id).
3. **SSL** → hPanel → Security → SSL → install the free certificate for your domain.

## 2. Upload & folder layout

Laravel's `public/` must be the web root — keep the framework OUTSIDE `public_html`:

```
/home/USERNAME/
├── lafitmeal/          ← the whole Laravel app (uploaded zip, extracted here)
│   ├── app/ bootstrap/ config/ ... vendor/
│   └── public/         ← not used directly
└── public_html/        ← contents of lafitmeal/public go here
```

1. File Manager → upload the zip to `/home/USERNAME/lafitmeal` and extract.
2. Copy **the contents of** `lafitmeal/public/` into `public_html/`
   (index.php, .htaccess, build/, favicon…).
3. Edit `public_html/index.php` and fix the two paths:

```php
require __DIR__.'/../lafitmeal/vendor/autoload.php';
$app = require_once __DIR__.'/../lafitmeal/bootstrap/app.php';
```

> **Composer note:** Hostinger Premium/Business include SSH. If you prefer not
> to upload `vendor/`, SSH in and run
> `cd ~/lafitmeal && composer install --no-dev --optimize-autoloader`.

## 3. Environment

Copy `.env.example` → `.env` inside `~/lafitmeal` and set:

```
APP_NAME=LaFitMeal
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=uXXXXXXXXX_lafitmeal
DB_USERNAME=uXXXXXXXXX_lafit
DB_PASSWORD=********

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

PAYMENT_DRIVER=offline        # switch to razorpay/stripe once implemented

MAIL_MAILER=smtp              # hPanel → Emails gives you SMTP credentials
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=orders@yourdomain.com
MAIL_PASSWORD=********
```

Then via SSH (or hPanel's terminal):

```
cd ~/lafitmeal
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force          # loads the 135 meals + admin user
php artisan storage:link             # links public_html/../lafitmeal/storage
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**storage symlink on shared hosting:** if `storage:link` puts the link in the
wrong place (it links `lafitmeal/public/storage`), recreate it manually:

```
ln -s ~/lafitmeal/storage/app/public ~/public_html/storage
```

## 4. The one cron job (critical)

hPanel → Advanced → Cron Jobs → add:

```
* * * * *  /usr/bin/php /home/USERNAME/lafitmeal/artisan schedule:run >> /dev/null 2>&1
```

This single entry drives everything: cutoff locking (every 15 min),
subscription completion (daily), and queue processing (every minute).

## 5. First login

- Admin panel: `https://yourdomain.com/admin`
  - `admin@lafitmeal.com` / `ChangeMe@123` — **change this immediately**
    (Admin → Users) and update the email.
- Settings such as the skip cutoff (`skip_cutoff_hours`) live in the
  `settings` table (manageable via any DB tool or a future Filament page).

## 6. Going live checklist

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] Admin password changed
- [ ] SSL active, `APP_URL` uses https
- [ ] Cron entry added and `php artisan schedule:list` shows 3 jobs
- [ ] Test signup → plan builder → checkout (offline gateway auto-confirms)
- [ ] Pick a payment gateway (Razorpay for India / Stripe international),
      implement it against `App\Services\Payments\PaymentGateway`, set
      `PAYMENT_DRIVER`, and register it in `AppServiceProvider`.

## Upgrading later

VPS is worth it once you have real traffic: real queue workers
(`php artisan queue:work` under supervisor), Redis cache, and zero
shared-hosting quirks. The app needs no code changes to move.
