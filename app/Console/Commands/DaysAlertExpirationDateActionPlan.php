<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\ActionPlans\Facades\ActionPlan;

class DaysAlertExpirationDateActionPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'days-alert-expiration-date-action-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos de alerta cuando la fecha de vencimiento de las actividades de los planes de acción está cercana según la configuración de los días';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = ConfigurationsCompany::findAllCompanyByKey('days_alert_expiration_date_action_plan');

        foreach ($companies as $key => $value)
        {
            ActionPlan::
                  company($value['company_id'])
                ->daysAlertExpirationDate($value['value'])
                ->sendMailAlert();
        }
    }
}
