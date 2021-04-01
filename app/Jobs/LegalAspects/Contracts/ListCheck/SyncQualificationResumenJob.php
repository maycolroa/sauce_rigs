<?php

namespace App\Jobs\LegalAspects\Contracts\ListCheck;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\ContractTrait;

class SyncQualificationResumenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ContractTrait;

    protected $contract;
    protected $qualification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contract, $qualification)
    {
      $this->contract = $contract;
      $this->qualification = $qualification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->reloadLiskCheckResumen($this->contract, $this->qualification);
    }
}
