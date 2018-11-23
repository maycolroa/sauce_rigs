<?php

namespace App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImport;

class AudiometryImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile;
    protected $user;
    protected $company_id;

    public function __construct(UploadedFile $file, $company_id, $user)
    {
      $this->nameFile = 'audiometrias_'.date("YmdHis").'.xlsx';
      Storage::disk('public')->putFileAs('import/1', $file, $this->nameFile);
      $this->company_id = $company_id;
      $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Excel::import(new AudiometryImport($this->company_id, $this->user), "/import/1/$this->nameFile", 'public');
      Storage::disk('public')->delete('import/1/'. $this->nameFile);
    }
}
