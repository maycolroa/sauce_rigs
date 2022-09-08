<?php

namespace App\Console\Commands;

use App\Facades\Configuration;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\General\LogFilesImport;
use Illuminate\Support\Facades\Storage;

class DeleteFilesImportS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-files-import-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borrado de archivos de importacion de s3';

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
        $days = Configuration::getConfiguration('days_delete_files_import_s3');

        $files = LogFilesImport::select('*')
        ->whereRaw("TIMESTAMPDIFF(DAY, DATE_FORMAT(created_at, '%Y-%m-%d'), CURDATE()) >= {$days}")
        ->get();

        foreach ($files as $file)
        {
            try
            {
                $replace = str_replace('https://appsauce.s3.amazonaws.com/', '', $file->file);
                
                Storage::disk('s3')->delete($replace);

                $file->delete();

            } catch (Exception $e) {
                $errors = "{$e->getMessage()} \n {$e->getTraceAsString()}";
                \Log::error($errors);
            }
        }
    }
}
