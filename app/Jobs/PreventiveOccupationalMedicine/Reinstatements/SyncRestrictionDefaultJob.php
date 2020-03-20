<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Restriction;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\RestrictionDefault;
use DB;

class SyncRestrictionDefaultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $restrictionDefault = RestrictionDefault::get();

        foreach ($restrictionDefault as $key => $value) 
        {
            $restriction = Restriction::query();
            $restriction->company_scope = $this->company_id;
            $restriction = $restriction->firstOrCreate(['name' => $value->name], 
                                                ['name' => $value->name, 'company_id' => $this->company_id]);
        }
    }
}
