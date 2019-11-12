<?php

namespace App\Jobs\LegalAspects\Contracts\Evaluations;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationExcel;
use App\Facades\Mail\Facades\NotificationMail;

class EvaluationExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $filters;
    protected $evaluation_contract_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $filters, $evaluation_contract_id = NULL)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->evaluation_contract_id = $evaluation_contract_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/evaluaciones_'.date("YmdHis").'.xlsx';
      Excel::store(new EvaluationExcel($this->company_id, $this->filters, $this->evaluation_contract_id),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de las evaluaciones')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de evaluaciones.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('contracts')
        ->event('Job: EvaluationExportJob')
        ->company($this->company_id)
        ->send();
    }
}
