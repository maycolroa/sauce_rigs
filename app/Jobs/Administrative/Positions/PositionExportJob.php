<?php

namespace App\Jobs\Administrative\Positions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Administrative\Positions\PositionsExcel;
use App\Facades\Mail\Facades\NotificationMail;

class PositionExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id)
    {
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
          $nameExcel = 'export/1/cargos_'.date("YmdHis").'.xlsx';
          Excel::store(new PositionsExcel($this->company_id),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
          
          $paramUrl = base64_encode($nameExcel);

          NotificationMail::
            subject('Exportación de Cargos')
            ->message('Se ha generado una exportación de cargos.')
            ->recipients($this->user)
            ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
            ->module('positions')
            ->subcopy('Este link es valido por 24 horas')
            ->event('Job: PositionExportJob')
            ->company($this->company_id)
            ->send();

      } 
      catch (\Exception $e)
      {
        \Log::info($e->getMessage());
        
          NotificationMail::
              subject('Exportación de Cargos')
              ->recipients($this->user)
              ->message('Se produjo un error durante el proceso de exportación de los cargos. Contacte con el administrador')
              ->module('users')
              ->event('Job: PositionExportJob')
              ->company($this->company_id)
              ->send();
      }
    }
}
