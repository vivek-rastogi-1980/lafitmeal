<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CompleteExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:complete-expired';

    protected $description = 'Mark active subscriptions as completed once their end date passes';

    public function handle(): int
    {
        $count = Subscription::where('status', 'active')
            ->whereDate('end_date', '<', today())
            ->update(['status' => 'completed']);

        $this->info("Completed {$count} subscription(s).");

        return self::SUCCESS;
    }
}
