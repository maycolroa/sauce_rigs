<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Models\System\Licenses\License;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\SendNotification;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Traits\ContractTrait;
use DB;

class NotificationSendContract extends Command
{
    use ContractTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification-send-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculo y envio de la informacion de las notificaciones masivas de contratistas';

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
        $now = Carbon::now();
        
        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $newletters = SendNotification::where('send', false)
                ->where('date_send', $now->format('Y-m-d'))
                ->where([
                    ['hour', '<=', $now->toTimeString()],
                    ['hour', '>=', $now->copy()->subHour()->toTimeString()]
                ])
                ->where('company_id', $company)
                ->withoutGlobalScopes()
                ->get();

            if ($newletters->count() > 0)
            {
                foreach ($newletters as $key => $newletter) 
                {                
                    if ($newletter->activities->count() > 0)
                    {
                        $activities_ids = [];

                        foreach ($newletter->activities as $value)
                        {
                            array_push($activities_ids, $value->id);
                        }

                        $information = DB::table('sau_user_information_contract_lessee')
                        ->selectRaw('MIN(user_id) AS user_id, information_id')
                        ->groupBy('information_id');

                        $contracts = ActivityContract::select(
                            'sau_ct_information_contract_lessee.id',
                            'sau_ct_information_contract_lessee.nit', 
                            'sau_ct_information_contract_lessee.social_reason', 
                            'sau_ct_information_contract_lessee.classification', 
                            'sau_users.name', 
                            'sau_users.email'
                        )
                        ->join('sau_ct_contracts_activities', 'sau_ct_contracts_activities.activity_id', 'sau_ct_activities.id')
                        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contracts_activities.contract_id')
                        ->join(DB::raw("({$information->toSql()}) as t"), function ($join) 
                            {
                                $join->on("t.information_id", "sau_ct_information_contract_lessee.id");
                            }
                        )
                        ->join('sau_users', 'sau_users.id', 't.user_id')
                        ->whereIn('sau_ct_activities.id', $activities_ids)
                        ->where('sau_ct_activities.company_id', $company)
                        ->withoutGlobalScopes()
                        ->get();

                        $emails_contracts = [];

                        foreach ($contracts as $key => $new) 
                        {
                            array_push($emails_contracts, $new->email);
                        }

                        foreach ($emails_contracts as $key => $email) 
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject($newletter->subject)
                                ->recipients($recipient)
                                ->message($newletter->body)
                                ->module('contracts')
                                ->event('Tarea programada: NotificationSendContract')
                                ->company($company)
                                ->send();
                        }
                    }
                    else
                    {
                        $contracts = ContractLesseeInformation::query();
                        $contracts->company_scope = $company;
                        $contracts = $contracts->join('sau_ct_notification_contracts', 'sau_ct_notification_contracts.contract_id', 'sau_ct_information_contract_lessee.id')->where('sau_ct_notification_contracts.notification_id', $newletter->id)->get();

                        if ($contracts->count() > 0)
                        {
                            $contracts_email = [];

                            foreach ($contracts as $contract)
                            {
                                $users = $this->getUsersContract($contract->id, $company, true);

                                if ($users[0]->active == 'SI')
                                    array_push($contracts_email, $users[0]->email);
                            }

                            foreach ($contracts_email as $key => $email) 
                            {
                                $recipient = new User(["email" => $email]); 

                                NotificationMail::
                                    subject($newletter->subject)
                                    ->recipients($recipient)
                                    ->message($newletter->body)
                                    ->module('contracts')
                                    ->event('Tarea programada: NotificationSendContract')
                                    ->company($company)
                                    ->send();
                            }
                        }
                    }

                    $newletter->send = true;
                    $newletter->save();
                }
            }
        }
    }
}
