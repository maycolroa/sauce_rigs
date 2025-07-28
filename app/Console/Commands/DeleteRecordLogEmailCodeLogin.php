<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\LogMails\LogMail;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Carbon\Carbon;

class DeleteRecordLogEmailCodeLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-record-log-email-code-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para borrar registros de los codigos para iniciar sesion de la tabla de logs de correos';

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
        $emails = LogMail::where('subject', 'Código para inicio de sesión')->delete();
    }
}
