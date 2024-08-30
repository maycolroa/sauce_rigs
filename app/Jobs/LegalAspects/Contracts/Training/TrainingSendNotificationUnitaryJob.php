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

class TrainingSendNotificationUnitaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;
    protected $training_id;
    protected $contract_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id = '', $training_id = '', $contract_id = '')
    {
      $this->company_id = $company_id;
      $this->training_id = $training_id;
      $this->contract_id = $contract_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {     
        $trainings = Training::select(
          'sau_ct_trainings.id as id',
          'sau_ct_trainings.name as name_training',
          'sau_ct_trainings.company_id as company_id',
          'sau_ct_contract_employees.id as id_employee',
          'sau_ct_contract_employees.name as name_employee',
          'sau_ct_contract_employees.email as email',
          'sau_ct_contract_employees.token as token',
          'sau_ct_information_contract_lessee.email_training_employee as email_supervisor_contract'
        )
        ->join('sau_ct_training_activity', 'sau_ct_training_activity.training_id', 'sau_ct_trainings.id')
        ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.activity_contract_id', 'sau_ct_training_activity.activity_id')
        ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.id', 'sau_ct_contract_employee_activities.employee_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.contract_id')
        ->where('sau_ct_information_contract_lessee.active', 'SI')
        ->where('sau_ct_trainings.active', 'SI')
        ->where('sau_ct_trainings.id', $this->training_id)
        ->whereIn('sau_ct_contract_employees.contract_id', $this->contract_id);

        $trainings->company_scope = $this->company_id;
        $trainings = $trainings->get();

        if ($trainings->count() > 0)
        {
          $listSupervisor = collect([]);
          $company = $this->company_id;

          $trainings = $trainings->groupBy('id_employee')
          ->each(function ($item, $keyEmployee) use ($company, &$listSupervisor) {
            $trainingsList = collect([]);

            $item->each(function ($training, $key) use ($keyEmployee, &$trainingsList) {
               $trainingSend = TrainingEmployeeSend::
                  where('employee_id', $keyEmployee)
                ->where('training_id', $training->id)
                ->orderBy('created_at', 'DESC')
                ->first();

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
                  $id_training = $value->id;
                  $token = $value->token;
                }
                
                $url = action('LegalAspects\Contracs\TrainingEmployeeController@index', ['training' => $value->id, 'token' => $token]);
                array_push($urls, $url);
                array_push($list, '<b>'.$value->name_training.'</b>');

                $listSupervisor->push([
                  'employee' => $value->name_employee,
                  'training' => '<b>'.$value->name_training.'</b>',
                  'url' => $url,
                  'supervisor' => $value->email_supervisor_contract,
                ]);
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

          foreach ($listSupervisor->groupBy('supervisor') as $key => $value)
          {
            if ($key)
            {
              NotificationMail::
                subject('Sauce - Contratistas Capacitaciones')
                ->view('LegalAspects.contract.trainingEmployeeSupervisor')
                ->recipients(collect([['email' => $key]]))
                ->message("Estimado usuario, estas son las capacitaciones que deben realizar los siguientes empleados, para hacerlo ingrese a los links indicados en la columna Capacitación: ")
                ->module('contracts')
                ->event('TrainingSendNotificationJob')
                ->with(['data'=>$value])
                ->company($company)
                ->send();
            }
          }          
        }
    }
}
