<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\General\EmailBlackList;
use App\Facades\Configuration;
use App\Models\Administrative\Users\User;
use App\Facades\Mail\Facades\NotificationMail;
use DB;
use Carbon\Carbon;

class SendBounceSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-bounce-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de listado de correos rechazados o rebotados por amazon';

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
        $result = EmailBlackList::select(
            DB::raw("COUNT(CASE WHEN problem_type = 'bounce' THEN 1 END) AS bounce"),
            DB::raw("COUNT(CASE WHEN problem_type = 'complaint' THEN 1 END) AS complaint")
        )
        ->whereDate('created_at', Carbon::now()->subDay()->format('Y-m-d'))
        ->first();

        $recipients = User::where('id', -1)->get();

        \Log::info($result);

        if ($result->bounce > 0 || $result->complaint > 0)
        {
            $emails = EmailBlackList::select('*')
            ->whereDate('created_at', Carbon::now()->subDay()->format('Y-m-d'))
            ->get();

            $emails_tables = [];

            foreach ($emails as $key => $value) 
            {
                $content = [
                    'Email' => $value->email,
                    'Tipo' => $value->problem_type == 'bounce' ? 'Rebotado' : ($value->problem_type == 'complaint' ? 'Marcados como Spam' : '')
                ];

                array_push($emails_tables, $content);
            }

            $administratorMails = Configuration::getConfiguration('admin_notification_email');
            $administratorMails = explode(",", $administratorMails);

            foreach ($administratorMails as $key => $value)
            {
                $recipients->push(new User(['email'=>$value]));
            }

            NotificationMail::
                subject('Resumen de correos rebotados')
                ->recipients($recipients)
                ->message('Listado de correos rebotadps')
                ->module('users')
                ->event('Tarea Programada: Resumen de rebotes')
                ->table($emails_tables)
                ->list([
                    "Rebotados: {$result->bounce}",
                    "Marcados como Spam: {$result->complaint}"
                ])
                ->company(1)
                ->send();

            
        }
    }
}
