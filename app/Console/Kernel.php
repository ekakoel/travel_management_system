<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('currency:refresh-rates')
            ->dailyAt('00:00')
            ->timezone(config('app.timezone'))
            ->withoutOverlapping(60)
            ->name('currency:refresh-rates');
        $schedule->command('activities:draft-expired')
            ->dailyAt('00:00')
            ->timezone(config('app.timezone'))
            ->withoutOverlapping(60)
            ->name('activities:draft-expired');
        $schedule->command('orders:auto-cancel-expired-payments')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->name('orders:auto-cancel-expired-payments');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
