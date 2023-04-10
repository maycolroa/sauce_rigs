<?php

namespace App\Jobs\System\Licenses;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Exports\System\Licenses\ReportExportExcel;

class ExportLicensesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $data)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $headers = [];

      foreach ($this->data['headers'] as $key => $value) 
      {
        foreach ($this->data['headers'][$key] as $key2 => $value) 
        {
            if ($key2 < 1)
              $headers[$key] = [];
            array_push($headers[$key], $value['label']);
        }
      }

      /*$headers['general'] = [                    
        'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
        'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
        'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
        'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
        'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
        'Total Periodo '.$dates[0].'/'.$dates[1],
        'Porcentaje de retención',
        'Porcentaje de crecimiento'
      ];

      $headers['group'] = [                       
          'Grupo de compañia',
          'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
          'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
          'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
          'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
          'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
          'Total Periodo '.$dates[0].'/'.$dates[1],
          'Porcentaje de retención',
          'Porcentaje de crecimiento'
      ];

      $headers['module'] = [      
          'Módulo',
          'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
          'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
          'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
          'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
          'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
          'Total Periodo '.$dates[0].'/'.$dates[1],
          'Porcentaje de retención',
          'Porcentaje de crecimiento'
      ];

      $headers['group_module'] = [      
          'Grupo de compañia',
          'Módulo',
          'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas',
          'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas',
          'Total Periodo '.$dates_old[0].'/'.$dates_old[1],
          'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas',
          'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas',
          'Total Periodo '.$dates[0].'/'.$dates[1],
          'Porcentaje de retención',
          'Porcentaje de crecimiento'
      ];

      $headers['group_module_not'] = [      
        'Grupo de compañia',
        'compañia',
        'Módulo'
      ];*/

      $this->data['headers'] = $headers;

      $nameExcel = 'export/1/licenses_report_'.date("YmdHis").'.xlsx';
      Excel::store(new ReportExportExcel($this->data),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de Reportes de Licencias')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de reportes de licencia.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('users')
        ->event('Job: ExportLicensesReportJob')
        ->company($this->company_id)
        ->send();
    }
}
