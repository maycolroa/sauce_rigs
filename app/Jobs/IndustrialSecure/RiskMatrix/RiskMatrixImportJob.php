<?php

namespace App\Jobs\IndustrialSecure\RiskMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndustrialSecure\RiskMatrix\RiskMatrixImport;
use App\Models\General\LogFilesImport;

class RiskMatrixImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile;
    protected $user;
    protected $company_id;

    public function __construct(UploadedFile $file, $company_id, $user)
    {
      $this->nameFile = 'matriz_riesgos_'.date("YmdHis").'.xlsx';

      Storage::disk('s3')->putFileAs('imports/files/', $file, $this->nameFile);
      Storage::disk('s3')->setVisibility("imports/files/{$this->nameFile}", 'public');

      Storage::disk('public')->putFileAs('import/1', $file, $this->nameFile);
      $this->company_id = $company_id;
      $this->user = $user;

      $recordImport = new LogFilesImport;
      $recordImport->company_id = $this->company_id;
      $recordImport->user_id = $this->user->id;
      $recordImport->file = Storage::disk('s3')->url('imports/files/' . $this->nameFile);
      $recordImport->module = "MAtriz de riesgos";
      $recordImport->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Excel::import(new RiskMatrixImport($this->company_id, $this->user), "/import/1/$this->nameFile", 'public');
      Storage::disk('public')->delete('import/1/'. $this->nameFile);
    }
}
