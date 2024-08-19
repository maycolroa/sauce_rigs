<?php

namespace App\Jobs\LegalAspects\Contracts\Employees;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LegalAspects\ContractEmployeeImportSocialSecure;
use App\Models\General\LogFilesImport;

class ContractEmployeeImportSocialSecureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile;
    protected $user;
    protected $company_id;
    protected $file_social_secure;
    protected $contract;

    public function __construct(UploadedFile $file, $company_id, $user, $contract, $file_social_secure)
    {
      $this->nameFile = 'contract_empleados_'.date("YmdHis").'.xlsx';

      Storage::disk('s3')->putFileAs('imports/files/', $file, $this->nameFile);
      Storage::disk('s3')->setVisibility("imports/files/{$this->nameFile}", 'public');
      
      Storage::disk('public')->putFileAs('import/1', $file, $this->nameFile);
      $this->company_id = $company_id;
      $this->user = $user;
      $this->contract = $contract;

      $recordImport = new LogFilesImport;
      $recordImport->company_id = $this->company_id;
      $recordImport->user_id = $this->user->id;
      $recordImport->file = Storage::disk('s3')->url('imports/files/' . $this->nameFile);
      $recordImport->module = "Contratistas Empleados";
      $recordImport->save();

      $this->file_social_secure = $file_social_secure;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Excel::import(new ContractEmployeeImportSocialSecure($this->company_id, $this->user, $this->contract, $this->file_social_secure), "/import/1/$this->nameFile", 'public');
      Storage::disk('public')->delete('import/1/'. $this->nameFile);
    }
}
