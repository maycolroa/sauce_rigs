<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Jobs\LegalAspects\Contracts\ListCheck\ListCheckContractExportJob;
use Carbon\Carbon;
use DB;

class NotifyUpdateListCheckContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-update-list-check-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificar sobre la actualización de la lista de chequeo de contratista';

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
        $ini = Carbon::now()->addDays(-1)->format('Y-m-d 00:00:00');
        $end = Carbon::now()->addDays(-1)->format('Y-m-d 23:59:59');

        $contracts = DB::table('sau_ct_item_qualification_contract')
            ->selectRaw('DISTINCT contract_id AS id')
            ->whereRaw("sau_ct_item_qualification_contract.updated_at BETWEEN '$ini' AND '$end'")
            ->get();

        foreach ($contracts as $value)
        {
            $contract = ContractLesseeInformation::withoutGlobalScopes()
            ->find($value->id);            ;
            ListCheckContractExportJob::dispatch($contract->company_id, $contract);
        }
    }
}
