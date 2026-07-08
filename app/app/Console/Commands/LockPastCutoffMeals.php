<?php

namespace App\Console\Commands;

use App\Models\MealSchedule;
use App\Models\Setting;
use Illuminate\Console\Command;

/**
 * Once a meal's skip-cutoff has passed, lock it so customers can no
 * longer skip it and the kitchen can rely on the day's counts.
 */
class LockPastCutoffMeals extends Command
{
    protected $signature = 'meals:lock-past-cutoff';

    protected $description = 'Lock scheduled meals whose skip-cutoff time has passed';

    public function handle(): int
    {
        $cutoffHours = (int) Setting::get('skip_cutoff_hours', 12);

        // A meal on date D is skippable while now < (D 00:00 − cutoff),
        // i.e. while D 00:00 > now + cutoff. So any date <= (now + cutoff)
        // has passed its cutoff and gets locked.
        $locked = MealSchedule::where('status', 'scheduled')
            ->whereDate('date', '<=', now()->addHours($cutoffHours)->toDateString())
            ->update(['status' => 'locked']);

        $this->info("Locked {$locked} meal(s).");

        return self::SUCCESS;
    }
}
