<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Traits\ContractTrait;
use Carbon\Carbon;
use DB;


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
            $companyContract = Company::find($company);

            $configDay = $this->getConfig($company);

            if (!$configDay)
                continue;

            $responsibles = collect([]);

            $listAlerts = collect([]);

            /**CONTRATISTAS SIN EMPLEADOS*/
            $employeeCounts = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id AS id, 
                sau_ct_information_contract_lessee.social_reason AS contract, 
                COUNT(sau_ct_contract_employees.id) AS employee
                ")
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

            /**FIN CONTRATISTAS CON ARCHIVOS VENCIDOS*/

            /**CONTRATISTAS CON ARCHIVOS DE EMPLEADOS REQUERIDOS*/

            $documentEmployee = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id AS id,
                sau_ct_information_contract_lessee.social_reason AS contract,
                sau_ct_contract_employees.name AS employee,
                sau_ct_activities.name As activity,
                sau_ct_activities_documents.name AS document,
                case when sau_ct_file_document_employee.employee_id is not null then 'SI' else 'NO' end as cargado,
                sau_ct_file_upload_contracts_leesse.updated_at AS file_date,
                sau_ct_information_contract_lessee.created_at AS created_at
                ")
            ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.employee_id', 'sau_ct_contract_employees.id')
            ->join('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contract_employee_activities.activity_contract_id')
            ->join('sau_ct_activities_documents', 'sau_ct_activities_documents.activity_id', 'sau_ct_activities.id')
            ->leftJoin('sau_ct_file_document_employee', function ($join) 
            {
                $join->on("sau_ct_file_document_employee.employee_id", "sau_ct_contract_employee_activities.employee_id");
                $join->on("sau_ct_file_document_employee.document_id", "sau_ct_activities_documents.id");
            })
            ->leftJoin('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_employee.file_id');

            $documentEmployee->company_scope = $company;
            $documentEmployee = $documentEmployee->get();

            if ($documentEmployee->count() > 0)
            {
                $NO = $documentEmployee->filter(function($item, $key) {
                    return $item->cargado == 'NO';
                })
                ->unique('contract');

                $SI = $documentEmployee->filter(function($item, $key) {
                    return $item->cargado == 'SI';
                });

                foreach ($NO as $contract) 
                {
                    $aux = $SI->where('id', $contract->id)->max('file_date');

                    if ($aux)
                    {
                        $dateUpload = Carbon::createFromFormat('Y-m-d H:i:s', $aux);

                        if ($dateUpload->diffInDays(Carbon::now()) >= $configDay)
                        {
                            if ($listAlerts->has($contract->id))
                            {
                                $employeeDocument = $listAlerts->get($contract->id);
                                $employeeDocument->push('Requiere documentos de empleados');
                                $listAlerts->put($contract->id, $employeeDocument);
                            }
                            else
                                $listAlerts->put($contract->id, collect(['Requiere documentos de empleados']));
                        }
                    }
                    else
                    {
                        if ($contract->created_at && $contract->created_at->diffInDays(Carbon::now()) >= $configDay)
                        {
                            if ($listAlerts->has($contract->id))
                            {
                                $employeeDocument = $listAlerts->get($contract->id);
                                $employeeDocument->push('Requiere documentos de empleados');
                                $listAlerts->put($contract->id, $employeeDocument);
                            }
                            else
                                $listAlerts->put($contract->id, collect(['Requiere documentos de empleados']));
                        }
                    }
                }
            }

            /**FIN CONTRATISTAS CON ARCHIVOS DE EMPLEADOS REQUERIDOS*/

            /**CONTRATISTAS CON ARCHIVOS GLOBALES PENDIENTES*/

            $documentGlobal = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id AS id,
                sau_ct_information_contract_lessee.social_reason AS contract,
                case when sau_ct_file_document_contract.contract_id is not null then 'SI' else 'NO' end as cargado,
                sau_ct_file_upload_contracts_leesse.updated_at AS file_date,
                sau_ct_information_contract_lessee.created_at AS created_at
                ")
            ->join('sau_ct_contracts_documents', 'sau_ct_contracts_documents.company_id', 'sau_ct_information_contract_lessee.company_id')
            ->leftJoin('sau_ct_file_document_contract', function ($join) 
            {
                $join->on("sau_ct_file_document_contract.contract_id", "sau_ct_information_contract_lessee.id");
                $join->on("sau_ct_file_document_contract.document_id", "sau_ct_contracts_documents.id");
            })
            ->leftJoin('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_contract.file_id');

            $documentGlobal->company_scope = $company;
            $documentGlobal = $documentGlobal->get();

            if ($documentGlobal->count() > 0)
            {
                $noGlobal = $documentGlobal->filter(function($item, $key) {
                    return $item->cargado == 'NO';
                })
                ->unique('contract');

                $siGlobal = $documentGlobal->filter(function($item, $key) {
                    return $item->cargado == 'SI';
                });

                foreach ($noGlobal as $contract) 
                {
                    $auxglobal = $siGlobal->where('id', $contract->id)->max('file_date');

                    if ($auxglobal)
                    {
                        $dateUploadGlobal = Carbon::createFromFormat('Y-m-d H:i:s', $auxglobal);

                        if ($dateUploadGlobal->diffInDays(Carbon::now()) >= $configDay)
                        {
                            if ($listAlerts->has($contract->id))
                            {
                                $globalDocument = $listAlerts->get($contract->id);
                                $globalDocument->push('Requiere documentos globales');
                                $listAlerts->put($contract->id, $globalDocument);
                            }
                            else
                                $listAlerts->put($contract->id, collect(['Requiere documentos globales']));
                        }
                    }
                    else
                    {
                        if ($contract->created_at && $contract->created_at->diffInDays(Carbon::now()) >= $configDay)
                        {
                            if ($listAlerts->has($contract->id))
                            {
                                $globalDocument = $listAlerts->get($contract->id);
                                $globalDocument->push('Requiere documentos globales');
                                $listAlerts->put($contract->id, $globalDocument);
                            }
                            else
                                $listAlerts->put($contract->id, collect(['Requiere documentos globales']));
                        }
                    }
                }
            }

            /**FIN CONTRATISTAS CON ARCHIVOS GLOBALES PENDIENTES*/

            /**CONTRATISTAS CON STANDARES PENDIENTES POR CALIFICAR*/

            $standardApply = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id AS contratista,
                CASE 
                    WHEN (classification = 'UPA' AND number_workers <= 10 AND risk_class IN ('Clase de riesgo I', 'Clase de riesgo II', 'Clase de riesgo III')) THEN '3 Estándares'
                    WHEN (classification = 'Empresa' AND number_workers <= 10 AND risk_class IN ('Clase de riesgo I', 'Clase de riesgo II', 'Clase de riesgo III')) THEN '7 Estándares'
                    WHEN (classification = 'Empresa' AND number_workers BETWEEN 11 AND 50 AND risk_class IN ('Clase de riesgo I', 'Clase de riesgo II', 'Clase de riesgo III')) THEN '21 Estándares'
                    WHEN (
                        (classification = 'UPA' AND number_workers <= 10 AND risk_class IN ('Clase de riesgo IV', 'Clase de riesgo V')) OR
                        (classification = 'Empresa' AND number_workers BETWEEN 11 AND 50 AND risk_class IN ('Clase de riesgo IV', 'Clase de riesgo V')) OR 
                        (classification = 'Empresa' AND number_workers > 50) OR (classification = 'Empresa' AND risk_class IN ('Clase de riesgo IV', 'Clase de riesgo V')) 
                        )THEN '60 Estándares'
                ELSE NULL END AS standard_names,
                sau_ct_information_contract_lessee.created_at AS created_at
                ")
            ->withoutGlobalScopes()
            ->where('sau_ct_information_contract_lessee.company_id', $company)        
            ->havingRaw('standard_names IS NOT NULL')
            ->orderBy('contratista');

            $standard = SectionCategoryItems::select(
                'sau_ct_standard_classification.standard_name',
                'sau_ct_section_category_items.id AS item_id')
            ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
            ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id');

            $pendientStandard = DB::table(DB::raw("({$standardApply->toSql()}) AS t"))
            ->selectRaw("
                contratista,
                SUM(CASE WHEN sau_ct_item_qualification_contract.qualification_id IS NULL THEN 1 ELSE 0 END) AS total_nc,
                MAX(sau_ct_item_qualification_contract.updated_at) AS updated_at,
                t.created_at
            ")
            ->join(DB::raw("({$standard->toSql()}) as t2"), function ($join) 
            {
                $join->on("t2.standard_name", "t.standard_names");
            })
            ->join('sau_ct_list_check_qualifications', function ($join) 
            {
                $join->on("sau_ct_list_check_qualifications.contract_id", "t.contratista");
                $join->on('sau_ct_list_check_qualifications.state', DB::raw(1));
            })
            ->leftJoin('sau_ct_item_qualification_contract', function ($join) 
            {
                $join->on("sau_ct_item_qualification_contract.item_id", "t2.item_id");
                $join->on("sau_ct_item_qualification_contract.list_qualification_id", "sau_ct_list_check_qualifications.id");
                $join->on("sau_ct_item_qualification_contract.contract_id", "t.contratista");
            })
            ->groupBy('contratista', 'standard_names')
            ->mergeBindings($standardApply->getQuery())
            ->mergeBindings($standard->getQuery())
            ->get();

            if ($pendientStandard->count() > 0)
            {
                $noStandard = $pendientStandard->filter(function($item, $key) {
                    return $item->total_nc > '0';
                });

                foreach ($noStandard as $contract) 
                {
                    $auxstandard = $noStandard->where('updated_at', '<>', 'null')->max('updated_at');

                    if ($auxstandard)
                    {
                        $datestandard = Carbon::createFromFormat('Y-m-d H:i:s', $auxstandard);

                        if ($datestandard->diffInDays(Carbon::now()) >= $configDay)
                        {
                            if ($listAlerts->has($contract->contratista))
                            {
                                $standarPendient = $listAlerts->get($contract->contratista);
                                $standarPendient->push('Estándares pendientes por calificar');
                                $listAlerts->put($contract->contratista, $standarPendient);
                            }
                            else
                                $listAlerts->put($contract->contratista, collect(['Estándares pendientes por calificar']));
                        }
                    }
                    else
                    {
                        $datestandard = Carbon::createFromFormat('Y-m-d H:i:s', $contract->created_at);

                        if ($datestandard && $datestandard->diffInDays(Carbon::now()) >= $configDay)
                        {
                            if ($listAlerts->has($contract->contratista))
                            {
                                $standarPendient = $listAlerts->get($contract->contratista);
                                $standarPendient->push('Estándares pendientes por calificar');
                                $listAlerts->put($contract->contratista, $standarPendient);
                            }
                            else
                                $listAlerts->put($contract->contratista, collect(['Estándares pendientes por calificar']));
                        }
                    }
                }
            }

            /**CONTRATISTAS CON STANDARES PENDIENTES POR CALIFICAR*/

            /**AGRUPACION POR RESPONSABLE**/

            foreach ($listAlerts as $keyContract => $alert)
            {
                $contractIter = ContractLesseeInformation::where('id', $keyContract);
                $contractIter->company_scope = $company;
                $contractIter = $contractIter->first();

                $recipients = $contractIter->responsibles;



                $recipients = $recipients->filter(function ($recipient, $index) use ($company) {
                  return $recipient->can('contracts_receive_notifications', $company) && !$recipient->isSuperAdmin($company);
                });

                foreach ($recipients as $recipient)
                {
                    if (!$responsibles->has($recipient->email))
                        $responsibles->put($recipient->email, collect([]));
                }

                foreach ($responsibles as $keyResponsible => $responsible)
                {
                    $iter = $responsibles->get($keyResponsible);
                    $iter->push([
                        'Contratista' => $contractIter->social_reason,
                        'Actividades pendientes' => $alert->implode(", ")
                    ]);
                }
            }

            /** FIN AGRUPACION POR RESPONSABLE**/

            /**ENVIO DE CORREO**/

            foreach ($responsibles as $key => $data)
            {
                $recipient = new User(["email" => $key]); 

                NotificationMail::
                    subject('Sauce - Contratistas contratistas sin actividad')
                    ->recipients($recipient)
                    ->message("Para la empresa <b>$companyContract->name</b>, este es el listado de las contratistas sin actividad en los ùltimos <b>$configDay</b> dias")
                    ->module('contracts')
                    ->event('Tarea programada: DaysAlertsWithoutActivityContractors')
                    ->table($data->toArray())
                    ->company($company)
                    ->send();
            }

            /**FIN ENVIODE CORREO**/
        }        
    }

    public function getConfig($company_id)
    {
        $key = "days_alert_without_activity";

        try
        {
            return ConfigurationsCompany::company($company_id)->findByKey($key);
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
