<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentFile;

class MigrateFilesLegalMatrixQualifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-files-legal-matrix-qualifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar archivos de calificacion de la tabla principal de calificaciones a la nueva tabla donde se contendran';

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
        $qualifications = ArticleFulfillment::withoutGlobalScopes()->whereNotNull('file')->get();

        foreach ($qualifications as $qualification) 
        {
            $file_new = new ArticleFulfillmentFile;
            $file_new->fulfillment_id = $qualification->id;
            $file_new->article_id = $qualification->article_id;
            $file_new->company_id = $qualification->company_id;
            $file_new->file = $qualification->file;
            $file_new->save();
        }
    }
}
