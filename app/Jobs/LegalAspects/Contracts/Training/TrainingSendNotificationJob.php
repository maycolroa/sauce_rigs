<?php

namespace App\Jobs\LegalAspects\Contracts\Training;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationContractExcel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\Training;
use App\Models\LegalAspects\Contracts\TrainingEmployeeSend;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\Administrative\Users\User;
use App\Models\System\Licenses\License;
use Carbon\Carbon;

class TrainingSendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;
    protected $training_id;
    protected $employee_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id = '', $training_id = '', $employee_id = '')
    {
      $this->company_id = $company_id;
      $this->training_id = $training_id;
      $this->employee_id = $employee_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

      if ($this->company_id)
        $companies->where('sau_licenses.company_id', $this->company_id);

      $companies = $companies->pluck('sau_licenses.company_id');

      foreach ($companies as $company)
      {
        $trainings = Training::select(
            'sau_ct_trainings.id as id',
            'sau_ct_trainings.name as name_training',
            'sau_ct_contract_employees.id as id_employee',
            'sau_ct_contract_employees.name as name_employee',
            'sau_ct_contract_employees.email as email',
            'sau_ct_contract_employees.token as token'
          )
          ->join('sau_ct_training_activity', 'sau_ct_training_activity.training_id', 'sau_ct_trainings.id')
          ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.activity_contract_id', 'sau_ct_training_activity.activity_id')
          ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.id', 'sau_ct_contract_employee_activities.employee_id')
          ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.contract_id')
          ->where('sau_ct_information_contract_lessee.active', 'SI')
          ->where('sau_ct_trainings.active', 'SI');

        $trainings->company_scope = $company;

        if ($this->training_id)
          $trainings->where('sau_ct_trainings.id', $this->training_id);

        if ($this->employee_id)
          $trainings->where('sau_ct_contract_employees.id', $this->employee_id);

        $trainings = $trainings->get();

        if ($trainings->count() > 0)
        {
          $trainings = $trainings->groupBy('id_employee')
          ->each(function ($item, $keyEmployee) use ($company) {
            $trainingsList = collect([]);

            $item->each(function ($training, $key) use ($keyEmployee, &$trainingsList) {
               $trainingSend = TrainingEmployeeSend::
                  where('employee_id', $keyEmployee)
                ->where('training_id', $training->id)
                ->orderBy('created_at', 'DESC')
                ->first();

                if ($trainingSend)
                {
                  if ($trainingSend->created_at->diffInDays(Carbon::now()) < 7)
                    return true;
                }

                $trainingsList->push($training);

            });

            if ($trainingsList->count() > 0)
            {
              $list = [];
              $urls = [];
              $email = '';
              $name = '';

              foreach ($trainingsList as $key => $value)
              {
                if ($key == 0)
                {
                  $email = $value->email;
                  $name = $value->name_employee;
                }
                
                $url = url("/");
                array_push($urls, $url);
                array_push($list, '<b>'.$value->name_training.'</b>');
              }

              $recipients = new User(['email' => $email]);

              NotificationMail::
                  subject('Sauce - Contratistas Capacitaciones')
                  ->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                  ->recipients($recipients)
                  ->message("Estimado $name, usted debe realizar las siguientes capacitaciones, para hacerlo ingrese a los links acontinuación: ")
                  ->module('contracts')
                  ->event('TrainingSendNotificationJob')
                  ->with(['user' => $name, 'urls'=>$urls])
                  ->list($list, 'ul')
                  ->company($company)
                  ->send();

              sleep(1);

              foreach ($trainingsList as $key => $value)
              {
                TrainingEmployeeSend::create(['training_id' => $value->id, 'employee_id' => $value->id_employee]);
              }
            }
          });
        }

        \Log::info($trainings);
      }
    }
}
