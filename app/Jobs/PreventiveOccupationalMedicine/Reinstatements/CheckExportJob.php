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
      try
      {
        $checks = Check::select('sau_reinc_checks.*')
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

        $checks = $checks->get();
        $dataMedicalMonitorings = collect([]);
        $dataLaborMonitorings = collect([]);
        $dataTracings = collect([]);
        $dataLaborNotes = collect([]);

        foreach ($checks as $check)
        {
          foreach ($check->medicalMonitorings as $medicalMonitoring)
          {
            $dataMedicalMonitorings->push($medicalMonitoring);
          }

          foreach ($check->laborMonitorings as $laborMonitorings)
          {
            $dataLaborMonitorings->push($laborMonitorings);
          }

          foreach ($check->tracings as $tracing)
          {
            $dataTracings->push($tracing);
          }

          foreach ($check->laborNotes as $note)
          {
            $dataLaborNotes->push($note);
          }
        }

        $data = collect([]);
        $data->put('checks', $checks);
        $data->put('medicalMonitorings', $dataMedicalMonitorings);
        $data->put('laborMonitorings', $dataLaborMonitorings);
        $data->put('tracings', $dataTracings);
        $data->put('laborNotes', $dataLaborNotes);

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

      } catch (\Exception $e)
      {
        \Log::info($e);
          NotificationMail::
              subject('Reincorporaciones: Exportación de los reportes')
              ->recipients($this->user)
              ->message('Se produjo un error durante el proceso de importación de los reportes. Contacte con el administrador')
              //->message($e->getMessage())
              ->module('reinstatements')
              ->event('Job: CheckExportJob')
              ->company($this->company_id)
              ->send();
      }

    }
}
