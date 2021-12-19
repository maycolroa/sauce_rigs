<?php

namespace App\Jobs\IndustrialSecure\Epp;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndustrialSecure\Epp\ElementBalanceInitialImport;
use App\Imports\IndustrialSecure\Epp\ElementBalanceInitialNotIdentyImport;

class ElementBalanceInitialImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile;
    protected $user;
    protected $company_id;
    protected $type_element;

    public function __construct($type_element, UploadedFile $file, $company_id, $user)
    {
      $this->nameFile = 'saldos_'.date("YmdHis").'.xlsx';
      Storage::disk('public')->putFileAs('import/1', $file, $this->nameFile);
      $this->company_id = $company_id;
      $this->user = $user;
      $this->type_element = $type_element;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      if ($this->type_element == 'Identificable')
        Excel::import(new ElementBalanceInitialImport($this->company_id, $this->user), "/import/1/$this->nameFile", 'public');
      else
        Excel::import(new ElementBalanceInitialNotIdentyImport($this->company_id, $this->user), "/import/1/$this->nameFile", 'public');

      Storage::disk('public')->delete('import/1/'. $this->nameFile);
    }
}
