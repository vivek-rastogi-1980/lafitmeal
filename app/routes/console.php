<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled jobs (driven by the single Hostinger cron entry:
|   * * * * * php /path/to/artisan schedule:run
|--------------------------------------------------------------------------
*/

// Enforce the skip cutoff so the kitchen counts stay reliable.
Schedule::command('meals:lock-past-cutoff')->everyFifteenMinutes();

// Close out plans that have run their course.
Schedule::command('subscriptions:complete-expired')->dailyAt('00:10');

// Shared-hosting-friendly queue processing (no daemon needed).
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
