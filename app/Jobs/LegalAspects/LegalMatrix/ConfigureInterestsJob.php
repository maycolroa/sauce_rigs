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
      $company->interests()->sync($this->interests);

      $this->syncQualificationsCompany($this->company_id);

      $users = User::withoutGlobalScopes()
            ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
            ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->where('sau_company_user.company_id', $this->company_id)
            ->whereRaw('(sau_roles.company_id = '.$this->company_id.' OR (sau_roles.company_id IS NULL AND sau_roles.name IN("Superadmin") ) )')
            ->get();

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
