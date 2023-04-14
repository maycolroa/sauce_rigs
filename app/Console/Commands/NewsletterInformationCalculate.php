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

class NewsletterInformationCalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter-information-calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculo de la informacion del envio de boletines';

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

        $newletter = NewsletterSend::where('active', true)
            ->where('send', false)
            ->where('date_send', $now->format('Y-m-d'))
            ->where([
                ['hour', '<=', $now->toTimeString()],
                ['hour', '>=', $now->copy()->subHour()->toTimeString()]
            ])
            ->get();

        if ($newletter->count() > 0)
        {
            $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')]);

            $companies = $companies->pluck('sau_licenses.company_id')->toArray();
            $roles = explode(',', ConfigurationsCompany::company(1)->findByKey('roles_newsletter'));

            $users = User::select('sau_users.email AS email')
            ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
            ->join('sau_teams', 'sau_teams.id', 'sau_role_user.team_id')
            ->whereIn('sau_role_user.role_id', $roles)
            ->whereIn('sau_teams.id', $companies)
            ->groupBy('sau_users.id')
            ->get()
            ->pluck('email')
            ->toArray();

            foreach ($newletter as $key => $news) 
            {
                foreach ($users as $key => $user) {
                    NewsletterSendUser::firstOrCreate(
                        [
                            'email' => $user,
                            'newsletter_id' => $news->id
                        ],
                        [
                            'email' => $user,
                            'newsletter_id' => $news->id,
                            'state' => false
                        ]
                    );
                }                
            }
        }
    }
}
