<?php

namespace App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\UtilsTrait;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Mail\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class AudiometryImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UtilsTrait;

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
      $context = ["--context_param ruta_archivo=".storage_path('app/public/import/1/'),
                  "--context_param nombre_archivo=".$this->nameFile,
                  "--context_param company_id=1",
                  "--context_param host=".DB::connection()->getConfig("host"),
                  "--context_param db=".DB::connection()->getConfig("database"),
                  "--context_param user=".DB::connection()->getConfig("username"),
                  "--context_param pass=".DB::connection()->getConfig("password")];

      
      implode(" ",$context);
      $pathJobTalend = storage_path('app/talend_jobs/') . 
                        $this->getValueFromEnvFile('NAME_JOB_TALEND_BIOLOGICAL_MONITORING_AUDIOMETRY_IMPORT') . '_' . 
                        $this->getValueFromEnvFile('VERSION_JOB_TALEND_BIOLOGICAL_MONITORING_AUDIOMETRY_IMPORT') . '/' .
                        $this->getValueFromEnvFile('NAME_JOB_TALEND_BIOLOGICAL_MONITORING_AUDIOMETRY_IMPORT') . '/' . 
                        $this->getValueFromEnvFile('NAME_JOB_TALEND_BIOLOGICAL_MONITORING_AUDIOMETRY_IMPORT') . '_run.sh';
      
      $process = new Process('sh '.$pathJobTalend . ' ' . implode(" ",$context));
      
      try {
        $process->mustRun();
        Storage::disk('public')->delete('import/1/'. $this->nameFile);

        Mail::to(Auth::user())->send(new AudiometryImportMail('El proceso de importacion de las audiometrias finalizo correctamente'));

      } catch (ProcessFailedException $exception) {
        \Log::error($exception->getMessage());
        Mail::to(Auth::user())->send(new AudiometryImportMail('Se produjo un error en el proceso de importacion de las audiometrias. Contacte con el administrador'));
      }
    }
}
