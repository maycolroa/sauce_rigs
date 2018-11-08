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
use App\Facades\Mail\Facades\NotificationMail;
use Illuminate\Support\Facades\Auth;
use Exception;

class AudiometryImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile;

    public function __construct(UploadedFile $file)
    {
      $this->nameFile = 'audiometrias_'.date("YmdHis").'.xlsx';
      Storage::disk('public')->putFileAs('import/1', $file, $this->nameFile);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    { 
      try
      {
        Excel::import(new AudiometryImport, "/import/1/$this->nameFile", 'public');
        Storage::disk('public')->delete('import/1/'. $this->nameFile);
        
      } catch (\Exception $e)
      {
        NotificationMail::
          subject('Importación de las audiometrias')
          ->recipients(Auth::user())
          ->message('Se produjo un error durante el proceso de importación de las audiometrias. Contacte con el administrador')
          ->module('biologicalMonitoring/audiometry')
          ->send();
      }
    }
}
