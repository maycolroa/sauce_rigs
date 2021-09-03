<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\General\Company;
use Carbon\Carbon;
use DB;
use App\Traits\ContractTrait;

class NotifyCreateContractEmployee extends Command
{
    use ContractTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-create-contract-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de notificar a los responsables del contratista la creacion de nuevos empleados';

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

        $results = ContractEmployee::select(
            'sau_ct_contract_employees.*',
            'sau_ct_information_contract_lessee.social_reason AS contract'
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.contract_id')
        ->whereRaw("sau_ct_contract_employees.created_at BETWEEN '$ini' AND '$end'")
        ->orderBy('contract')
        ->get();

        $results = $results->groupBy('company_id');

        foreach ($results as $keyCompany => $company)
        {
            $companyContract = Company::find($keyCompany);

            $responsibles = collect([]);
            $contracts = $company->groupBy('contract_id');

            foreach ($contracts as $keyContract => $employees)
            {
                $contractIter = ContractLesseeInformation::where('id', $keyContract);
                $contractIter->company_scope = $keyCompany;
                $contractIter = $contractIter->first();

                $recipients = $contractIter->responsibles;

                $recipients = $recipients->filter(function ($recipient, $index) use ($keyCompany) {
                  return $recipient->can('contracts_receive_notifications', $keyCompany) && !$recipient->isSuperAdmin($keyCompany);
                });

                foreach ($recipients as $recipient)
                {
                    if (!$responsibles->has($recipient->email))
                        $responsibles->put($recipient->email, collect([]));
                }

                foreach ($employees as $employee)
                {
                    foreach ($responsibles as $keyResponsible => $responsible)
                    {
                        $iter = $responsibles->get($keyResponsible);
                        $iter->push([
                            'Identificación' => $employee->identification,
                            'Nombre' => $employee->name,
                            'Cargo' => $employee->position,
                            'Contratista' => $employee->contract
                        ]);
                    }
                }
            }

            foreach ($responsibles as $key => $data)
            {
                $recipient = new User(["email" => $key]); 

                NotificationMail::
                    subject('Sauce - Contratistas creación de nuevos empleados')
                    ->recipients($recipient)
                    ->message("Para la empresa <b>$companyContract->name</b>, fueron creados los siguientes empleados de contratistas: ")
                    ->module('contracts')
                    ->event('Tarea programada: NotifyCreateContractEmployee')
                    ->table($data->toArray())
                    ->company($keyCompany)
                    ->send();
            }
        }
        
        //\Log::info($responsibles);
    }
}
