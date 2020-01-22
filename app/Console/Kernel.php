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
        /*'App\Console\Commands\CheckLastLoginNotification',
        'App\Console\Commands\DisableUsers',*/
        'App\Console\Commands\DaysAlertExpiredLicense',
        'App\Console\Commands\NotifyUpdateLaws',
        'App\Console\Commands\ReincSendMail',
        'App\Console\Commands\ReincNotificationNextFollowUp',
        'App\Console\Commands\DmReportHistory',
        'App\Console\Commands\NotifyUpdateListCheckContract'
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

        /*$schedule->command('checkLastLoginNotification')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');*/

        /*$schedule->command('disableUsers')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');*/

        $schedule->command('days-alert-expired-license')
            ->weekly()
            ->sundays()
            ->timezone('America/Bogota')
            ->at('01:00');

        $schedule->command('notify-update-laws')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');
        
        $schedule->command('dm-report-history')
            ->timezone('America/Bogota')
            ->cron('0 1 1 1-12/3 *');

        $schedule->command('reinc-send-mail')
            ->timezone('America/Bogota')
            ->dailyAt('22:00');

        $schedule->command('reinc-notification-next-follow-up')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('notify-update-list-check-contract')
            ->timezone('America/Bogota')
            ->dailyAt('02:00');
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
