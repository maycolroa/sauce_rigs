<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\System\Licenses\License;
use Carbon\Carbon;

class UpdateEmployeeContratFilesExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-employee-contrat-files-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizacion de estado de los documentos de empleados, cuando los archivos se vencen';

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
        ->where('sau_license_module.module_id', 16 /*32 prod, 34 local*/);

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $contracts = ContractLesseeInformation::select('id')->where('company_id', $company)->withoutGlobalScopes()->isActive()->get();

            foreach ($contracts as $key2 => $contract) 
            {
                if ($contract->id == 14)
                {
                    $employees = ContractEmployee::where('contract_id', $contract->id)->withoutGlobalScopes()->get();

                    foreach ($employees as $key3 => $employee) 
                    {
                        $pendiente = false;
                        $activities = $employee->activities->transform(function($activity, $index) use ($employee) {
                            $activity->documents = $this->getFilesByActivity($activity->id, $employee->id, $employee->contract_id);
            
                            return $activity;
                        });

                        foreach ($activities as $key4 => $activity) 
                        {
                            foreach ($activity->documents as $key5 => $document) 
                            {                                
                                foreach ($document->files as $key6 => $file) 
                                {
                                    if ($file->expirationDate <= date('Y-m-d'))
                                        $pendiente = true;
                                }
                            }
                        }

                        if ($pendiente)
                        {
                            $employee->state = 'Pendiente';
                            $employee->save();
                        }
                    }
                }
            }
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
