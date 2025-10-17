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
        //'App\Console\Commands\AudiometryNotification',
        'App\Console\Commands\DaysAlertExpirationDateActionPlan',
        'App\Console\Commands\DaysAlertExpirationDateContractFilesUpload',
        /*'App\Console\Commands\CheckLastLoginNotification',
        'App\Console\Commands\DisableUsers',*/
        'App\Console\Commands\DaysAlertExpiredLicense',
        'App\Console\Commands\NotifyUpdateLaws',
        'App\Console\Commands\ReincSendMail',
        //'App\Console\Commands\ReincNotificationNextFollowUp',
        'App\Console\Commands\DmReportHistory',
        'App\Console\Commands\RmReportHistory',
        'App\Console\Commands\NotifyUpdateListCheckContract',
        'App\Console\Commands\CtUnlockEvaluation',
        //'App\Console\Commands\DaysAlertsWithoutActivityContractors',
        'App\Console\Commands\NotifyUploadDocumentsContracts',
        'App\Console\Commands\NotifyRejectedDocumentsContracts',
        'App\Console\Commands\DeleteOldImagesApi',
        'App\Console\Commands\DeleteFilesTemporal',
        'App\Console\Commands\SendAutomaticNotifications',
        'App\Console\Commands\NotificationRequestFirmInspection',
        'App\Console\Commands\UpdateEppElementsBelowStock',
        'App\Console\Commands\DaysAlertExpiredElementsAsigned',
        'App\Console\Commands\NotifyAlertStockMinimun',
        'App\Console\Commands\SendBounceSummary',
        'App\Console\Commands\DeleteFilesImportS3',
        'App\Console\Commands\NotifyReportsOpenConfig',
        'App\Console\Commands\ReincPendienteResumen',
        'App\Console\Commands\ReportGroupCompany',
        'App\Console\Commands\NewsletterInformationCalculate',
        'App\Console\Commands\SendNewsletterEmail',
        'App\Console\Commands\UpdateEmployeeContratFilesExpired',
        'App\Console\Commands\NotificationSendContract',
        'App\Console\Commands\NotifyNextMaintenanceVehicle',
        'App\Console\Commands\UpdateContractEmployeeStateDocuments',       
        'App\Console\Commands\DeleteRecordLogEmailCodeLogin',
        'App\Console\Commands\UpdateContractEmployeeStateDocuments',
        'App\Console\Commands\UpdateStateEmployeeAdministrative',
        //'App\Console\Commands\RememberRepeatInspetion'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('delete-record-log-email-code-login')
            ->timezone('America/Bogota')
            ->cron('0 23 * * 0');


        $schedule->command('days-alert-expiration-date-action-plan')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('telescope:prune')->daily();

        $schedule->command('days-alert-expiration-date-contract-files-upload')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');

        $schedule->command('notify-next-maintenance-vehicle')
            ->timezone('America/Bogota')
            ->dailyAt('06:00');

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
        
        $schedule->command('rm-report-history')
            ->timezone('America/Bogota')
            ->cron('0 1 1 1-12/3 *');

        $schedule->command('reinc-send-mail')
            ->timezone('America/Bogota')
            ->dailyAt('06:00');

        $schedule->command('reinc-pendiente-resumen')
            ->timezone('America/Bogota')
            ->cron('0 6 1 * *');

        $schedule->command('report-group-company')
            ->timezone('America/Bogota')
            ->cron('0 7 1 * *');

        /*$schedule->command('reinc-notification-next-follow-up')
            ->timezone('America/Bogota')
            ->dailyAt('01:00');*/

        $schedule->command('notify-update-list-check-contract')
            ->timezone('America/Bogota')
            ->dailyAt('02:00');

        $schedule->command('notify-upload-documents-contracts')
            ->timezone('America/Bogota')
            ->dailyAt('02:00');

        /*$schedule->command('days-alerts-without-activity-contractors')
            ->timezone('America/Bogota')
            ->dailyAt('02:00');*/

        $schedule->command('notify-rejected-documents-contracts')
            ->timezone('America/Bogota')
            ->dailyAt('0 */2 * * *');


        $schedule->command('delete-old-images-api')
            ->timezone('America/Bogota')
            ->dailyAt('03:00');

        $schedule->command('send-automatic-notifications')
            ->timezone('America/Bogota')
            ->dailyAt('03:00');

        /*$schedule->command('delete-files-temporal')
            ->timezone('America/Bogota')
            ->dailyAt('03:00');*/

        /*$schedule->command('remember-repeat-inspetion')
            ->timezone('America/Bogota')
            ->dailyAt('02:00');*/

        $schedule->command('ct-unlock-evaluation')
            ->timezone('America/Bogota')
            ->everyMinute();

        $schedule->command('notification-request-firm-inspection')
            ->timezone('America/Bogota')
            ->dailyAt('12:00');

        $schedule->command('days-alert-expired-elements-asigned')
            ->timezone('America/Bogota')
            ->dailyAt('3:00');

        $schedule->command('update-epp-elements-below-stock')
            ->timezone('America/Bogota')
            ->everyMinute();

        $schedule->command('notify-alert-stock-minimun')
            ->timezone('America/Bogota')
            ->dailyAt('3:10');

        $schedule->command('send-bounce-summary')
            ->timezone('America/Bogota')
            ->dailyAt('7:00');
        
        $schedule->command('delete-files-import-s3')
            ->timezone('America/Bogota')
            ->dailyAt('3:30');

        $schedule->command('notify-reports-open-config')
            ->timezone('America/Bogota')
            ->dailyAt('4:00');
        
        $schedule->command('newsletter-information-calculate')
            ->timezone('America/Bogota')
            ->cron('*/60 * * * *');

        $schedule->command('send-newsletter-email')
            ->timezone('America/Bogota')
            ->cron('*/2 * * * *');

        $schedule->command('license-report-send')
            ->timezone('America/Bogota')
            ->cron('0 8 1 1-12/1 *');

        $schedule->command('update-employee-contrat-files-expired')
            ->timezone('America/Bogota')
            ->dailyAt('01:45');
        
        $schedule->command('notification-send-contract')
            ->timezone('America/Bogota')
            ->cron('*/60 * * * *');

        $schedule->command('update-contract-employee-state-documents')
            ->timezone('America/Bogota')
            ->dailyAt('05:00');

        $schedule->command('update-state-employee-administrative')
            ->timezone('America/Bogota')
            ->dailyAt('05:00');
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
