<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\System\Licenses\License;
use Carbon\Carbon;
use DB;

class UpdateContractEmployeeStateDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-contract-employee-state-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizacion del estado de los documentos de los empleados contratistas segun los documentos cargados';

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

      DB::beginTransaction();

      try
      {
        $companies = License::selectRaw('DISTINCT company_id')
        ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
        ->withoutGlobalScopes()
        ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
        ->where('sau_license_module.module_id', 16);

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            \Log::info('company: '. $company);
            $contracts = ContractLesseeInformation::select('id')->where('company_id', $company)->withoutGlobalScopes()->isActive()->get();

            foreach ($contracts as $key2 => $contract) 
            {
                \Log::info('contract: '. $contract->id);
                $employees = ContractEmployee::where('contract_id', $contract->id)
                ->withoutGlobalScopes()->get();

                foreach ($employees as $key3 => $employee) 
                {
                    $pendiente = false;
                    $rejected = false;
                    $expired = false;
                    $activity_asigned = false;

                    $activities = $employee->activities->transform(function($activity, $index) use ($employee) {
                        $activity->documents = $this->getFilesByActivity($activity->id, $employee->id, $employee->contract_id);
        
                        return $activity;
                    });

                    
                    $files_states = [];

                    foreach ($activities as $key4 => $activity) 
                    {
                        $activity_asigned = true;
                        $documents_counts = ActivityDocument::where('activity_id', $activity->id)->where('type', 'Empleado')->withoutGlobalScopes()->get();

                        $documents_counts = $documents_counts->count();
                        $count = 0;

                        foreach ($activity->documents as $key5 => $document) 
                        {                       
                            $count_files = COUNT($document->files);
                            $count_aprobe = 0;      

                            foreach ($document->files as $key6 => $file) 
                            {
                                if ($file->expirationDate)
                                {
                                    if ($file->expirationDate > date('Y-m-d'))
                                    {
                                        if ($file->state == 'ACEPTADO')
                                        {
                                            $count_aprobe++;
                                            $rejected = false;
                                            $expired = false;
                                            $pendiente = false;
                                        }
                                        else if ($file->state == 'RECHAZADO')
                                        {
                                            $rejected = true;
                                            $pendiente = false;
                                            $expired = false;
                                        }
                                        else if ($file->state == 'PENDIENTE')
                                        {
                                            $rejected = false;
                                            $pendiente = true;
                                            $expired = false;
                                        }
                                    }
                                    else
                                    {
                                        if ($file->state == 'RECHAZADO')
                                        {
                                            $rejected = true;
                                            $pendiente = false;
                                            $expired = true;
                                        }
                                        else if ($file->state == 'PENDIENTE')
                                        {
                                            $rejected = false;
                                            $pendiente = true;
                                            $expired = true;
                                        }
                                    }
                                }
                                else
                                {
                                    if ($file->state == 'ACEPTADO')
                                    {
                                        $count_aprobe++;
                                        $rejected = false;
                                        $pendiente = false;
                                        $expired = false;
                                    }
                                    else if ($file->state == 'RECHAZADO')
                                    {
                                        $rejected = true;
                                        $pendiente = false;
                                        $expired = false;
                                    }
                                    else if ($file->state == 'PENDIENTE')
                                    {
                                        $rejected = false;
                                        $pendiente = true;
                                        $expired = false;
                                    }
                                }
                                
                                if ($count_files == ($key+1))
                                {
                                    if ($fileUpload->state == 'ACEPTADO' && $expired)
                                        array_push($files_states, 'PENDIENTE');
                                    else
                                        array_push($files_states, $fileUpload->state);
                                }
                            }

                            if ($count_files > 0 && $count_aprobe >= $count_files)
                                $count++;
                            /*else if (!$pendiente && !$rejected && !$expired)
                                $count++;
                            else if ($count_files < 1)
                                $pendiente = true;*/
                        }
                    }

                    if (in_array('RECHAZADO', $files_states))
                    {
                        $employee->update(
                            [ 'state' => 'Rechazado']
                        );
                    }
                    else if ($documents_counts > $count)
                    {
                        $employee->update(
                            [ 'state' => 'Pendiente']
                        );
                    }
                    else if (in_array('PENDIENTE', $files_states))
                    {
                        $employee->update(
                            [ 'state' => 'Pendiente']
                        );
                    }
                    else if(in_array('ACEPTADO', $files_states))
                    {
                        $employee->update(
                        [ 'state' => 'Aprobado']
                        );
                    }
                }
            }
        }

        DB::commit();

       }   catch(\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
       }
    }

    public function getFilesByActivity($activity, $employee_id, $contract_id)
    {
        $documents = ActivityDocument::where('activity_id', $activity)->where('type', 'Empleado')->get();

        if ($documents->count() > 0)
        {
            $contract = $contract_id;
            $documents = $documents->transform(function($document, $key) use ($contract, $employee_id) {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
                $document->files = [];

                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.name AS name',
                    'sau_ct_file_upload_contracts_leesse.file AS file',
                    'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate',
                    'sau_ct_file_upload_contracts_leesse.state AS state',
                    'sau_ct_file_upload_contracts_leesse.reason_rejection AS reason_rejection'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->whereNotNull('sau_ct_file_upload_contracts_leesse.expirationDate')
                ->where('sau_ct_file_upload_contract.contract_id', $contract)
                ->where('sau_ct_file_document_employee.document_id', $document->id)
                ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                ->get();

                if ($files)
                {
                    $files->transform(function($file, $index) {
                        $file->expirationDate = $file->expirationDate == null ? null : $file->expirationDate;

                        return $file;
                    });

                    $document->files = $files;
                }

                return $document;
            });
        }

        return $documents;
    }
}
