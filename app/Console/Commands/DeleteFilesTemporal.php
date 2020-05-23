<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Facades\Configuration;

class DeleteFilesTemporal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-files-temporal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra los archivos temporales del sistema que tengas mas de X dias (configurable)';

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
        $paths = [
            "app/public/export/*",
            "app/public/import/*"
        ];

        $days = Configuration::getConfiguration('days_delete_files_temporal');

        try
        {
            foreach ($paths as $key => $value) 
            {
                $route = storage_path($value);

                $command = "find {$route} -mtime +{$days} -delete";
                \Log::info($command);
                $process = Process::fromShellCommandline($command);

                $process->run();

                // executes after the command finishes
                if ($process->isSuccessful())
                    \Log::info("Directorio limpiado: ". $route);
                else 
                    throw new ProcessFailedException($process);
            }
        }
        catch(\Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
