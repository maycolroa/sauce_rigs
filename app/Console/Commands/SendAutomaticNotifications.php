<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\System\NotificationManager;

class SendAutomaticNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-automatic-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio automatico de notificaciones';

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
        NotificationManager::sendNotification();
    }
}
