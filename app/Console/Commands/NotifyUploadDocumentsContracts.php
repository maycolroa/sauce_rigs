<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\General\Company;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\ContractDocument;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Models\System\Licenses\License;
use DB;


class NotifyUploadDocumentsContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-upload-documents-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica la carga de archivos en el modulo de contratistas';

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
        $date = Carbon::now()->subDay()->format('Y-m-d');
        $date = '2024-10-13';

        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $companyContract = Company::find($company);

            $responsibles = collect([]);

            $contracts = ContractLesseeInformation::where('company_id', $company)
            ->isActive();
            $contracts->company_scope = $company;
            $contracts = $contracts->get();

            foreach ($contracts as $key => $contract) 
            {
                $uploadDocuments = FileModuleState::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_module_state.state AS state',
                    'sau_ct_file_upload_contracts_leesse.name as name',
                    'sau_ct_file_module_state.module AS module',
                    'sau_ct_contract_employees.name AS employee'
                )
                ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_module_state.file_id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.id', 'sau_ct_file_document_employee.employee_id')
                ->where('date', $date)
                ->where('sau_ct_file_module_state.contract_id', $contract->id)
                ->where('sau_ct_contract_employees.contract_id', $contract->id)
                ->whereIn('sau_ct_file_module_state.state', ['CREADO', 'MODIFICADO'])
                ->get();

                if (COUNT($uploadDocuments) > 0)
                {
                    $recipients = $contract->responsibles;

                    $recipients = $recipients->filter(function ($recipient, $index) use ($company) {
                      return $recipient->can('contracts_receive_notifications', $company) && !$recipient->isSuperAdmin($company);
                    });

                    foreach ($recipients as $recipient)
                    {
                        if (!$responsibles->has($recipient->email))
                            $responsibles->put($recipient->email, collect([]));
                    }

                    foreach ($uploadDocuments as $document)
                    {
                        foreach ($responsibles as $keyResponsible => $responsible)
                        {
                            $iter = $responsibles->get($keyResponsible);
                            $iter->push([
                                'Contratista' => $contract->social_reason,
                                'Empleado' => $document->employee,
                                'Código' => $document->id,
                                'Documento' => $document->name,
                                'Módulo' => $document->module,
                                'Estado' => $document->state
                            ]);
                        }
                    }                    
                }
                else
                    continue;

            }

            foreach ($responsibles as $key => $data)
            {                
                $recipient = new User(["email" => $key]); 

                NotificationMail::
                    subject('Sauce - Contratistas Carga de archivos')
                    ->recipients($recipient)
                    ->message("Para la empresa <b>$companyContract->name</b>, este es el listado de los archivos cargados el dia de ayer")
                    ->module('contracts')
                    ->event('Tarea programada: NotifyUploadDocumentsContracts')
                    ->table($data->toArray())
                    ->company($company)
                    ->send();
            }

            /**FIN ENVIODE CORREO**/
        }        
    }
}
