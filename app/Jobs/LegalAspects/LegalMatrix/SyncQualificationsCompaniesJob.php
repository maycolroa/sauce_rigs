<?php

namespace App\Jobs\LegalAspects\LegalMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\LegalMatrixTrait;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Jobs\LegalAspects\LegalMatrix\UpdateQualificationsRepelead;

class SyncQualificationsCompaniesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LegalMatrixTrait;

    protected $law_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($law_id) 
    {
        $this->law_id = $law_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $this->syncQualificationsCompanies($this->law_id);

      $law = Law::find($this->law_id);

      if ($law->repealed == 'SI')
      {
          UpdateQualificationsRepelead::dispatch($law);
      }
    }
}
