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

class NotifyRejectedDocumentsContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-rejected-documents-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica si algun documento cargado es rechazado o modificados por el contratante';

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

        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $usersCreator = collect([]);

            $contracts = ContractLesseeInformation::where('company_id', $company)
            ->isActive()->where('id', 74);
            $contracts->company_scope = $company;
            $contracts = $contracts->get();

            foreach ($contracts as $key => $contract) 
            {
                $data = collect([]);

                $uploadDocuments = FileModuleState::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.state AS state',
                    'sau_ct_file_upload_contracts_leesse.name AS name',
                    'sau_ct_file_module_state.module AS module',
                    'sau_users.email AS email',
                    'sau_ct_file_upload_contracts_leesse.reason_rejection AS motivo'
                )
                ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_module_state.file_id')
                ->join('sau_ct_file_upload_contract', 'sau_ct_file_upload_contract.file_upload_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_users', 'sau_users.id', 'sau_ct_file_upload_contracts_leesse.user_id')
                ->where('date', $date)
                ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                ->where('sau_ct_file_module_state.contract_id', $contract->id)
                ->whereIn('sau_ct_file_upload_contracts_leesse.state', ['RECHAZADO', 'ACEPTADO'])
                ->get();

                if (COUNT($uploadDocuments) > 0)
                {
                    foreach ($uploadDocuments as $document)
                    {
                        $data->put($document->email, collect([]));
                    }

                    foreach ($uploadDocuments as $document)
                    {
                        $iter = $data->get($document->email);
                        $iter->push([
                            'Código' => $document->id,
                            'Documento' => $document->name,
                            'Módulo' => $document->module,
                            'Estado' => $document->state,
                            'Motivo del rechazo' => $document->motivo
                        ]);
                    }

                    foreach ($data as $key => $iter)
                    {          
                        $recipient = User::where('email', $key)->first();

                        try
                        {         
                            NotificationMail::
                                subject('Sauce - Contratistas Carga de archivos')
                                ->recipients($recipient)
                                ->message("Listado de archivos aceptados,rechazados por su contratante el dia de ayer")
                                ->module('contracts')
                                ->event('Tarea programada: NotifyRejectedDocumentsContracts')
                                ->table($iter->toArray())
                                ->company($company)
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Log::info($e->getMessage());
                            continue;
                        }         
                    }                            
                }
                else
                    continue;

            }
        }
    }
}
