<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Facades\Mail\Facades\NotificationMail;
use Carbon\Carbon;
use DB;

class NotifyUpdateLaws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-update-laws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificar sobre la actualización de leyes o articulos de la matriz legal';

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
        $companies = DB::table('sau_lm_company_interest')
            ->selectRaw('DISTINCT company_id AS id, sau_companies.name AS name')
            ->join('sau_companies', 'sau_companies.id', 'sau_lm_company_interest.company_id')
            ->where('sau_companies.active', 'SI')
            ->get();

        foreach ($companies as $company)
        {
            $users = User::select('sau_users.*')
                        ->active()
                        ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                        ->where('sau_company_user.company_id', $company->id)
                        ->get();

            $users->map(function($user) use ($company)
            {
                $ini = Carbon::now()->addDays(-1)->format('Y-m-d 00:00:00');
                $end = Carbon::now()->addDays(-1)->format('Y-m-d 23:59:59');

                $laws = Law::selectRaw(
                        'sau_lm_laws.id AS id,
                        SUBSTRING(sau_lm_laws.description, 1, 50) AS description,
                        sau_lm_laws.law_number,
                        sau_lm_laws.law_year,
                        sau_lm_laws_types.name AS type'
                    )
                    ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
                    ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
                    ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
                    ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
                    ->alls($company->id)
                    ->whereRaw("(sau_lm_laws.updated_at BETWEEN '$ini' AND '$end' OR 
                    sau_lm_articles.updated_at BETWEEN '$ini' AND '$end')")
                    ->groupBy('sau_lm_laws.id');

                $laws->company_scope = $company->id;
                $laws->user = $user->id;
                $laws = $laws->get();

                if (COUNT($laws) > 0)
                {
                    $list = [];
                    $urls = [];

                    foreach ($laws as $law)
                    {
                        $url = url("/legalaspects/lm/lawsQualify/view/{$law->id}");
                        array_push($urls, $url);
                        array_push($list, $law->type.' '.$law->law_number.' de '.$law->law_year.': '.$law->description.'...');
                    }

                    NotificationMail::
                        subject('Sauce - Matriz Legal Normas Modificadas')
                        ->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                        ->recipients($user)
                        ->message('Las siguientes normas fueron modificadas: ')
                        ->buttons([['text'=>'Ir al sitio', 'url'=>url("/legalaspects/lm/lawsQualify")]])
                        ->module('legalMatrix')
                        ->event('Tarea programada: NotifyUpdateLaws')
                        ->with(['user'=>$user->name, 'urls'=>$urls])
                        ->list($list, 'ul')
                        ->company($company->id)
                        ->send();

                    sleep(1);
                }
            });
        }
    }
}
