<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\LegalMatrixTrait;

class SyncQualificationsCompanies extends Command
{
    use LegalMatrixTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-qualifications-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->syncQualificationsCompanies();
    }
}
