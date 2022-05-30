<?php

namespace App\Jobs\IndustrialSecure\AccidentsWork;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\AccidentsWork\AccidentExcel;
use App\Facades\Mail\Facades\NotificationMail;

class AccidentsExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $filters;
    protected $id;

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
      $nameExcel = 'export/1/formularios_accidentes_'.date("YmdHis").'.xlsx';
      Excel::store(new AccidentExcel($this->company_id, $this->filters),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de los formularios de accidentes')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de accidentes.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('accidentsWork')
        ->event('Job: AccidentsExportJob')
        ->company($this->company_id)
        ->send();
    }
}
