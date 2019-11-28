<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
use App\Models\General\Configuration;

class SyncReincOptionsSelectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;
    protected $key;
    protected $table;
    protected $config;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id, $key, $table) 
    { 
        $this->company_id = $company_id;
        $this->key = $key;
        $this->table = $table;

        $this->config = Configuration::where('sau_configuration.key', $this->key)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $optionsSelect = DB::table($this->table)
            ->where('company_id', $this->company_id)
            ->pluck('name');

        if (COUNT($optionsSelect) > 0)
        {
            $value = [
                'company_id' => $this->company_id,
                'key' => $this->key,
                'value' => json_encode($optionsSelect),
                'observation' => $this->config ? $this->config->observation : ''
            ];

            $configCompany = DB::table('sau_configuration_company')
                ->updateOrInsert(['key' => $this->key, 'company_id' => $this->company_id], $value);
        }
        else
            DB::table('sau_configuration_company')
                ->where('company_id', $this->company_id)
                ->where('key', $this->key)
                ->delete();
    }
}
