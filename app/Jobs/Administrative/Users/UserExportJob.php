<?php

namespace App\Jobs\Administrative\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Administrative\Users\UsersExcel;
use App\Facades\Mail\Facades\NotificationMail;

class UserExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $filters;
    protected $company_id;

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
          $nameExcel = 'export/1/usuarios_'.date("YmdHis").'.xlsx';
          Excel::store(new UsersExcel($this->company_id, $this->filters),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
          
          $paramUrl = base64_encode($nameExcel);

          NotificationMail::
            subject('Exportación de Usuarios')
            ->message('Se ha generado una exportación de usuarios.')
            ->recipients($this->user)
            ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
            ->module('users')
            ->subcopy('Este link es valido por 24 horas')
            ->event('Job: UserExportJob')
            ->company($this->company_id)
            ->send();

      } 
      catch (\Exception $e)
      {
        \Log::info($e->getMessage());
        
          NotificationMail::
              subject('Exportación de Usuarios')
              ->recipients($this->user)
              ->message('Se produjo un error durante el proceso de exportación de los usuarios. Contacte con el administrador')
              ->module('users')
              ->event('Job: UserExportJob')
              ->company($this->company_id)
              ->send();
      }
    }
}
