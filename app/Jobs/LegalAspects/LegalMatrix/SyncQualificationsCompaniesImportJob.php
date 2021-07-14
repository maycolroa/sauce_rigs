<?php

namespace App\Jobs\LegalAspects\LegalMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\LegalMatrixTrait;

class SyncQualificationsCompaniesImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LegalMatrixTrait;

    protected $company_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id) 
    {
        $this->company_id = $company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $this->syncQualificationsCompany($this->company_id);
    }
}
