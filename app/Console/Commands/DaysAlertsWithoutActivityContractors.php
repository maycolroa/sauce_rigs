<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationsCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\System\Licenses\License;
use App\Traits\ContractTrait;

class DaysAlertsWithoutActivityContractors extends Command
{

    use ContractTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'days-alerts-without-activity-contractors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia correos con la lista de contratistas que tienen pendientes completar datos y ya tienen el tiempo limite sin actividad dentro de la plataforma vencidos';

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
        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $configs = $this->getConfig($company);

            $listAlerts = collect([]);

            /**CONTRATISTAS SIN EMPLEADOS*/
            $employeeCounts = ContractLesseeInformation::selectRaw("sau_ct_information_contract_lessee.id AS id, sau_ct_information_contract_lessee.social_reason AS contract, 
                COUNT(sau_ct_contract_employees.id) AS employee")
            ->leftJoin('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->groupBy('id', 'contract')
            ->havingRaw('COUNT(sau_ct_contract_employees.id) = 0')
            ->isActive();

            $employeeCounts->company_scope = $company;
            $employeeCounts = $employeeCounts->get();

            foreach ($employeeCounts as $employeeCount)
            {
                $listAlerts->put($employeeCount->id, collect(['Ingresar empleados']));
            }

            /**FIN CONTRATISTAS SIN EMPLEADOS*/

            /**CONTRATISTAS CON ARCHIVOS VENCIDOS*/
            $expiredDocuments = ContractLesseeInformation::selectRaw("sau_ct_information_contract_lessee.id AS id, sau_ct_information_contract_lessee.social_reason AS contract")
            ->join('sau_ct_file_upload_contract', 'sau_ct_file_upload_contract.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_upload_contract.file_upload_id')
            ->whereRaw('sau_ct_file_upload_contracts_leesse.expirationDate < DATE_ADD(CURDATE(), INTERVAL -30 DAY)')
            ->groupBy('id', 'contract');

            $expiredDocuments->company_scope = $company;
            $expiredDocuments = $expiredDocuments->get();

            foreach ($expiredDocuments as $expiredDocument)
            {
                if ($listAlerts->has($expiredDocument->id))
                {
                    $item = $listAlerts->get($expiredDocument->id);
                    $item->push('Documentos vencidos');
                    $listAlerts->put($expiredDocument->id, $item);
                }
                else
                    $listAlerts->put($expiredDocument->id, collect(['Documentos vencidos']));
            }

           // \Log::info($expiredDocuments); 

            /**FIN CONTRATISTAS CON ARCHIVOS VENCIDOS*/

            \Log::info($listAlerts);
        }        
    }

    public function getConfig($company_id)
    {
        $key = "days_alert_without_activity";

        try
        {
            return ConfigurationsCompany::company($company_id)->findByKey($key);
            
        } catch (Exception $e) {
            return 30;
        }
    }
}
