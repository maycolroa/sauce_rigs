<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Absenteeism\FileUpload;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Talend;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Facades\Mail\Facades\NotificationMail;
use Artisan;

class ProcessFileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    protected $user;
    protected $company_id;
    protected $file;
    protected $talend;

    CONST states = [
        0 => 'corrido fallido',
        1 => 'corrido exitoso 100%',
        2 => 'corrido parcialmente exitoso'
    ];

    CONST result_talend = 'resultado_talend:';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $file, $talend)
    {
        $this->user = $user;
        $this->company_id = $company_id;
        $this->file = $file;
        $this->talend = $talend;
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
            \Log::info("Inicio de análisis de archivo con id: {$this->file->id}\n");
            \Log::info("Archivo sh: {$this->talend->path_sh()}\n");
            \Log::info("Ruta de archivo: {$this->file->path}\n");
            \Log::info("Archivo: {$this->file->file}\n");

            $this->file->update(['state' => 'Procesando']);

            /*$process = new Process([
                'sh', $this->talend->path_sh(),
                $this->file->path, //Ruta archivo
                $this->file->file //Archivo
            ]);*/

            $command = "sh {$this->talend->path_sh()} '{$this->file->path}' '{$this->file->file}'";
            \Log::info($command);
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(1800);
            $process->run();

            \Log::info("Salida del talend: \n");
            \Log::info($process->getOutput());

            // executes after the command finishes
            if ($process->isSuccessful())
            {
                $output = strtolower($process->getOutput());
                $result = Str::substr($output, strpos($output, $this::result_talend) + 17, 1);

                if ($result && $result == '1')
                {
                    foreach ($this::states as $key => $state)
                    {
                        if (strpos($output, $state) !== false)
                        {
                            $result = $state;
                            break;
                        }
                        else
                            $result = $this::states[0];
                    }
                }
                else
                    $result = $this::states[0];
            }
            else 
                $result = $this::states[0];
                //throw new ProcessFailedException($process);

            $this->file->update(['state' => $result, 'state_file' => $process->getOutput()]);

            NotificationMail::
                subject('Ausentismo - carga de archivo')
                ->recipients($this->user)
                ->message("El proceso de carga de archivo solicitado ha culminado obteniendo el siguiente resultado: <span style='font-weight: bold;'>{$result}</span>")
                ->module('absenteeism')
                ->event('Job: ProcessFileUploadJob')
                ->company($this->company_id);

            if ($this::states[2] == $result)
                NotificationMail::attach("{$this->file->path}registros_inconsistentes.xlsx");

            NotificationMail::send();

            if ($result != $this::states[0])
                Artisan::call('notify-expired-absenteeism');
        }
        catch(\Exception $e) {
            \Log::info($e->getMessage());
            NotificationMail::
                subject('Ausentismo - carga de archivo')
                ->recipients($this->user)
                ->message("Se produjo un error durante el proceso. Contacte con el administrador")
                ->module('absenteeism')
                ->event('Job: ProcessFileUploadJob')
                ->company($this->company_id)
                ->send();
        }

        \Log::info("Fin de análisis de archivo con id: {$this->file->id}\n");
    }
}
