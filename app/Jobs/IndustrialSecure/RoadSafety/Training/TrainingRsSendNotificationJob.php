<?php

namespace App\Jobs\IndustrialSecure\RoadSafety\Training;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\IndustrialSecure\RoadSafety\Training\Training;
use App\Models\IndustrialSecure\RoadSafety\Training\TrainingEmployeeSend;
use App\Models\Administrative\Employees\Employee;
use App\Models\Administrative\Users\User;
use App\Models\System\Licenses\License;
use Carbon\Carbon;

class TrainingRsSendNotificationJob implements ShouldQueue
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
            ->where('sau_license_module.module_id', '38');

      if ($this->company_id)
        $companies->where('sau_licenses.company_id', $this->company_id);

      $companies = $companies->pluck('sau_licenses.company_id');

      foreach ($companies as $company)
      {
        $trainings = Training::select(
            'sau_rs_trainings.id as id',
            'sau_rs_trainings.name as name_training',
            'sau_rs_trainings.company_id as company_id',
            'sau_employees.id as id_employee',
            'sau_employees.name as name_employee',
            'sau_employees.email as email',
            'sau_employees.token as token'
          )
          ->join('sau_rs_training_position', 'sau_rs_training_position.training_id', 'sau_rs_trainings.id')
          ->join('sau_employees_positions', 'sau_employees_positions.id', 'sau_rs_training_position.position_id')
          ->join('sau_employees', 'sau_employees.employee_position_id', 'sau_employees_positions.id')
          ->join('sau_rs_drivers', 'sau_rs_drivers.employee_id', 'sau_employees.id')
          ->where('sau_rs_trainings.active', 'SI');

        $trainings->company_scope = $company;

        if ($this->training_id)
          $trainings->where('sau_rs_trainings.id', $this->training_id);

        if ($this->employee_id)
          $trainings->where('sau_employees.id', $this->employee_id);

        $trainings = $trainings->get();

        if ($trainings->count() > 0)
        {
          $listSupervisor = collect([]);

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
                
                $url = action('IndustrialSecure\RoadSafety\Training\TrainingEmployeeController@index', ['training' => $value->id, 'token' => $token]);
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
                  subject('Sauce - Seguridad Vial Capacitaciones')
                  ->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                  ->recipients($recipients)
                  ->message("Estimado $name, usted debe realizar las siguientes capacitaciones, para hacerlo ingrese a los links acontinuación: ")
                  ->module('roadSafety')
                  ->event('TrainingRsSendNotificationJob')
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

          /*foreach ($listSupervisor->groupBy('supervisor') as $key => $value)
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
          }*/          
        }
      }
    }
}
