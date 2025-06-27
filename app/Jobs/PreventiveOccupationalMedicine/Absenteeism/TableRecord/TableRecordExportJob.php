<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Absenteeism\TableRecord;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\Absenteeism\TableRecordExcel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;
use DB;

class TableRecordExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $table;
    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($table, $user, $company_id)
    {
      $this->table = $table;
      $this->user = $user;
      $this->company_id = $company_id;
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
          $nameExcel = 'export/1/ausentismo_datos_'.date("YmdHis").'.xlsx';
          Excel::store(new TableRecordExcel($this->table, $this->company_id),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
          
          $paramUrl = base64_encode($nameExcel);

          NotificationMail::
            subject('Exportación de Ausentismo - Datos')
            ->message('Se ha generado una exportación de ausentismo - datos.')
            ->recipients($this->user)
            ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
            ->module('absenteeism')
            ->subcopy('Este link es valido por 24 horas')
            ->event('Job: TableRecordExportJob')
            ->company($this->company_id)
            ->send();

      } 
      catch (\Exception $e)
      {
        \Log::info($e->getMessage());
        
          NotificationMail::
              subject('Exportación de Ausentismo - Datos')
              ->recipients($this->user)
              ->message('Se produjo un error durante el proceso de exportación de los datos. Contacte con el administrador')
              ->module('absenteeism')
              ->event('Job: TableRecordExportJob')
              ->company($this->company_id)
              ->send();
      }
    }
}
