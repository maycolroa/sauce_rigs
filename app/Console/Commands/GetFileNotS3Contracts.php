<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\System\Licenses\License;
use Illuminate\Support\Facades\Storage;

class GetFileNotS3Contracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-file-not-s3-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtener los archivos en la bbdd que no esten en s3 para corregir errores';

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

        $files_faltantes = [];

        foreach ($companies as $key => $value)
        {
            $company_id = $value;

            $files_company = [];

            $contracts = ContractLesseeInformation::where('company_id', $company_id)
            ->withoutGlobalScopes()->isActive()->get();

            foreach ($contracts as $key => $contract) 
            {
                $files_contracts = [];

                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                ->whereNotNull('sau_ct_file_document_employee.employee_id')->withoutGlobalScopes()
                ->get();

                foreach ($files as $key => $value)
                {
                    $file_delete = FileUpload::withoutGlobalScopes()->where('id', $value->id)->first();

                    if ($file_delete)
                    {
                        if ($file_delete->apply_file && $file_delete->apply_file == 'SI')
                        {
                            $path = $file_delete->file;

                            if (!Storage::disk('s3')->exists('legalAspects/files/'. $path))
                                array_push($files_contracts, $path);
                        }
                        else
                            continue;
                    }
                }
            }
        }
    }
}
