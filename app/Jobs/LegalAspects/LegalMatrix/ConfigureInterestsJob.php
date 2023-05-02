<?php

namespace App\Jobs\LegalAspects\LegalMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\LegalMatrix\Interest;
use App\Traits\LegalMatrixTrait;

class ConfigureInterestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LegalMatrixTrait;

    protected $company_id;
    protected $interests;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id, $interests)
    {
      $this->company_id = $company_id;
      $this->interests = $interests;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $company = Company::find($this->company_id);
      $company_interests = Interest::where('company_id', $this->company_id)->pluck('id')->toArray();
      $interests = array_merge($this->interests, $company_interests);
      $company->interests()->sync($interests);

      $this->syncQualificationsCompany($this->company_id);

      UpdateQualificationsRepeleadCompany::dispatch($this->company_id);

      $users = User::select('sau_users.*')
                ->active()
                ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');      
      
      $users->company_scope = $this->company_id;

      $users = $users->get();

      $users = $users->filter(function ($user, $index) {
          return $user->can('legalMatrix_receive_notifications', $this->company_id);
        });


      if (!empty($users))
      {
        NotificationMail::
            subject('Matriz Legal - Configuración de intereses exitosa
            ')
            ->recipients($users)
            ->message("La configuración de intereses para la Matriz Legal de la empresa $company->name ha terminado con éxito.")
            ->buttons([['text'=>'Ir a Matriz Legal', 'url'=>url("/legalaspects/lm/interests/myinterests")]])
            ->module('legalMatrix')
            ->event('Job: ConfigureInterestsJob')
            ->company($this->company_id)
            ->send();
      }
    }
}
