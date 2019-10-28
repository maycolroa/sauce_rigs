<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Absenteeism\Talends;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Talend;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExtractTalendZipFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $talend;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($talend) {
        $this->talend = $talend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $process = new Process([
            'unzip', '-qo', $this->talend->path_from_extract(),  //-qo Extraer y sobreescribir archivo
            '-d', $this->talend->path_to_extract() //-d en el directorio indicado
        ]);

        $process->run();

        // executes after the command finishes
        if ($process->isSuccessful())
            $this->talend->update(['state' => 'SI']);
        else 
            throw new ProcessFailedException($process);
    }
}
