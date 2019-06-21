<?php

namespace App\Jobs\LegalAspects\LegalMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\LegalMatrixTrait;

class SyncQualificationsCompaniesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LegalMatrixTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $this->syncQualificationsCompanies();
    }
}
