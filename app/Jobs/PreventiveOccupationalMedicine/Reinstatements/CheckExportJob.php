<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\CheckExportExcel;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Facades\Mail\Facades\NotificationMail;

class CheckExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $filters;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $filters)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->filters = $filters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $checks = Check::select('sau_reinc_checks.*')/*with([
        'employee' => function ($query) {
            $query->with('regional', 'eps', 'position', 'business');
        },
        'cie10Code',
        'restriction',
        'medicalMonitorings',
        'laborMonitorings',
        'tracings.madeBy'
      ])*/
      ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
      ->inIdentifications($this->filters['identifications'], $this->filters['filtersType']['identifications'])
      ->inNames($this->filters['names'], $this->filters['filtersType']['names'])
      ->inRegionals($this->filters['regionals'], $this->filters['filtersType']['regionals'])
      ->inBusinesses($this->filters['businesses'], $this->filters['filtersType']['businesses'])
      ->inDiseaseOrigin($this->filters['diseaseOrigin'], $this->filters['filtersType']['diseaseOrigin'])
      ->betweenDate($this->filters["dates"]);

      if ($this->filters["nextFollowDays"])
              $checks->inNextFollowDays($this->filters["nextFollowDays"], $this->filters['filtersType']['nextFollowDays']);

      $checks->user = $this->user->id;
      $checks->company_scope = $this->company_id;

      \Log::info($checks->toSql());
      \Log::info("----------------------------------------------------------");
      \Log::info("----------------------------------------------------------");

      $checks = $checks->get();
      $dataMedicalMonitorings = collect([]);

      foreach ($checks as $check)
      {
          //\Log::info($check->id);
          foreach ($check->medicalMonitorings as $medicalMonitoring)
          {
            $dataMedicalMonitorings->push($medicalMonitoring);
          }
      }

      $data = collect([]);
      $data->put('medicalMonitorings', $dataMedicalMonitorings);
      
      //\Log::info($dataMedicalMonitorings);

      $nameExcel = 'export/1/reincorporaciones_reportes_'.date("YmdHis").'.xlsx';
      Excel::store(new CheckExportExcel($this->company_id, $data), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Reincorporaciones: Exportación de los reportes')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de reportes.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('reinstatements')
        ->event('Job: CheckExportJob')
        ->company($this->company_id)
        ->send();
    }
}
