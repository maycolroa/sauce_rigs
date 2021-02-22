<?php

namespace App\Jobs\System\UsersCompanies;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\System\UsersCompanies\UsersCompaniesExcel;
use App\Facades\Mail\Facades\NotificationMail;

class UserCompaniesExportJob implements ShouldQueue
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
    public function __construct($user, $filters, $company_id)
    {
      $this->user = $user;
      $this->filters = $filters;
      $this->company_id = $company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/usuarios_'.date("YmdHis").'.xlsx';
      Excel::store(new UsersCompaniesExcel($this->filters),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
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
}
