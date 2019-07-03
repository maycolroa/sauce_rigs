<?php

namespace App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryExcel;
use App\Facades\Mail\Facades\NotificationMail;

class AudiometryExportJob implements ShouldQueue
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
      $audiometries = Audiometry::select(
        'sau_bm_audiometries.*',
        'sau_employees.identification as employee_identification',
        'sau_employees.name as employee_name'
      )
      ->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
      ->inRegionals($this->filters["regionals"], $this->filters['filtersType']['regionals'])
      ->inHeadquarters($this->filters["headquarters"], $this->filters['filtersType']['headquarters'])
      ->inAreas($this->filters["areas"], $this->filters['filtersType']['areas'])
      ->inProcesses($this->filters["processes"], $this->filters['filtersType']['processes'])
      ->inPositions($this->filters["positions"], $this->filters['filtersType']['positions'])
      ->inDeals($this->filters["deals"], $this->filters['filtersType']['deals'])
      ->inYears($this->filters["years"], $this->filters['filtersType']['years'])
      ->inNames($this->filters["names"], $this->filters['filtersType']['names'])
      ->inIdentifications($this->filters["identifications"], $this->filters['filtersType']['identifications'])
      ->inSeverityGradeLeft($this->filters["severity_grade_left"], $this->filters['filtersType']['severity_grade_left'])
      ->inSeverityGradeRight($this->filters["severity_grade_right"], $this->filters['filtersType']['severity_grade_right'])
      ->betweenDate($this->filters["dates"]);

      $audiometries->company_scope = $this->company_id;

      $nameExcel = 'export/1/audiometrias_'.date("YmdHis").'.xlsx';
      Excel::store(new AudiometryExcel($audiometries->get()),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de las audiometrias')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de audiometrias.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('biologicalMonitoring/audiometry')
        ->event('Job: AudiometryExportJob')
        ->send();
    }
}
