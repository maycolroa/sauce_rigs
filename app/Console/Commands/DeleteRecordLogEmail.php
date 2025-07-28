<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\LogMails\LogMail;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Carbon\Carbon;

class DeleteRecordLogEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-record-log-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para borrar registros muy viejos de la tabla de logs de correos';

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
        //$emails = LogMail::get();

        $date = Carbon::now()->subMonth(10)->format('Y-m-d 00:00:00');

        $emails = LogMail::where('created_at', '<', $date)->get();

        try 
        {
            $days = ConfigurationsCompany::company(1)->findByKey('delete_records_log_mails');
            $now = Carbon::now();
            $count = 0;

            \Log::info($days);
            \Log::info('comenzo la tarea');
            \Log::info(Carbon::now());

            foreach ($emails as $key => $email) 
            {
                $diff = $now->diffInDays($email->created_at);

                if ($diff >= $days)
                {
                    $count++;
                    $email->delete();
                }
            }

            \Log::info($count);
            \Log::info(Carbon::now());
            \Log::info('Termino la tarea');
            
        } catch (\Exception $e) {                
            \Log::info('No se ha configurado el parametro');
        }
    }
}
