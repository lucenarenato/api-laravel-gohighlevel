<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('check-session:status')->everySixHours();
        // $schedule->command('sync:ghl-locations')->hourly();
        $schedule->command('sync:ghl-contacts')->everySixHours();
        $schedule->command('check-reservation:status')->everySixHours();
        $schedule->command('sync:refresh-ghl-integration')->twiceDaily(1,13);
        $schedule->command('ghl-source-token:refresh')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
