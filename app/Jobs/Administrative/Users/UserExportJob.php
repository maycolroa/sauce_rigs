<?php

namespace App\Jobs\Administrative\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Administrative\Users\UsersExcel;
use App\Facades\Mail\Facades\NotificationMail;

class UserExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
      $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $users = User::select(
            'sau_users.*',
            'sau_roles.name as role_name'
        )->join('sau_role_user','sau_role_user.user_id','sau_users.id')
        ->join('sau_roles','sau_roles.id','sau_role_user.role_id');

      $nameExcel = 'export/1/usuarios_'.date("YmdHis").'.xlsx';
      Excel::store(new UsersExcel($users->get()),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);

      NotificationMail::
        subject('Lista de Usuarios')
        ->message('Se ha generado una exportación de usuarios.')
        ->recipients($this->user)
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('users')
        ->subcopy('Este link es valido por 24 horas')
        ->send();
    }
}
