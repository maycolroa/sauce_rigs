<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\General\Company;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;

class MigrateColumnModuleFilesContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-column-module-files-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llenar la columna modulo de la tabla de archivo de contratistass';

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
        $files = FileUpload::withoutGlobalScopes()->whereNull('module')->limit(5000)->get();

        \Log::info($files->count());

        foreach ($files as $key => $file) 
        {
            $module = FileModuleState::where('file_id', $file->id)->first();
            \Log::info($module);

            if ($module)
            {
                $file->module = $module->module;
                $file->save();
            }
        }
    }
}
