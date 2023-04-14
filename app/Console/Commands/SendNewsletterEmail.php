<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Models\System\Licenses\License;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\General\NewsletterSendUser;
use App\Models\General\NewsletterSend;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SendNewsletterEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-newsletter-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de boletin a usuarios que cumplan con los roles seleccionados';

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
        $users_send = NewsletterSendUser::where('state', false)->limit(50)->get();

        if ($users_send)
        {
            foreach ($users_send as $user) 
            {
                $newletter = NewsletterSend::find($user->newsletter_id);
                $newletter->path = Storage::disk('s3')->url('newsletters/'. $newletter->image);
            
                $recipient = (new User(['email'=>$user->email]));

                NotificationMail::
                    subject($newletter->subject)
                    ->view('system.newsletters.newsletter')
                    ->recipients($recipient)
                    ->module('users')
                    ->event('Tarea programada: SendNewsletterEmail')
                    ->with(['data' => $newletter])
                    ->company(1)
                    ->send();

                $user->update([
                    'state' => true
                ]);
            }
        }

    }
}
