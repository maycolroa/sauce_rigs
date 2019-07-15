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
        'App\Console\Commands\AudiometryNotification',
        'App\Console\Commands\DaysAlertExpirationDateActionPlan',
        'App\Console\Commands\DaysAlertExpirationDateContractFilesUpload',
        'App\Console\Commands\CheckLastLoginNotification',
        'App\Console\Commands\DisableUsers',
        'App\Console\Commands\DaysAlertExpiredLicense',
        'App\Console\Commands\NotifyUpdateLaws'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('audiometry-notification')
            ->timezone('America/Bogota')
            ->dailyAt('00:00');

        $schedule->command('days-alert-expiration-date-action-plan')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('telescope:prune')->daily();

        $schedule->command('days-alert-expiration-date-contract-files-upload')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('checkLastLoginNotification')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('disableUsers')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('days-alert-expired-license')
            ->weekly()
            ->sundays()
            ->timezone('America/Bogota')
            ->at('01:00');

        $schedule->command('NotifyUpdateLaws')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');
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
