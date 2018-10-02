<?php

namespace App\Jobs\Administrative\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Administrative\Users\UsersExcel;
use App\Facades\Mail\Facades\NotificationMail;

class UserExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $users = User::select('*');

      $nameExcel = 'export/1/usuarios_'.date("YmdHis").'.xlsx';
      Excel::store(new UsersExcel($users->get()),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);

      NotificationMail::
        subject('Lista de Usuarios')
        ->recipients(Auth::user())
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('users')
        ->send();
    }
}
