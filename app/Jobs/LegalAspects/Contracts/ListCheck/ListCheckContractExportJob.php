<?php

namespace App\Jobs\LegalAspects\Contracts\ListCheck;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LegalAspects\Contracts\ListCheck\ListCheckContractExcel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Traits\ContractTrait;

class ListCheckContractExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ContractTrait;

    protected $company_id;
    protected $contract;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id, $contract)
    {
      $this->company_id = $company_id;
      $this->contract = $contract;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$recipients = $this->getUsersMasterContract($this->company_id);
        $recipients = $this->contract->responsibles;

        $recipients = $recipients->filter(function ($recipient, $index) {
          return $recipient->can('contracts_receive_notifications', $this->company_id);
        });
        
        if (!$recipients->isEmpty())
        {
          $nameExcel = 'export/1/lista_estandares_minimos_'.date("YmdHis").'.xlsx';
          Excel::store(new ListCheckContractExcel($this->contract),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
          
          $paramUrl = base64_encode($nameExcel);
          
          NotificationMail::
            subject('Contratistas - Lista de estándares mínimos actualizada')
            ->recipients($recipients)
            ->message('La contratista '.$this->contract->nit.' - '.$this->contract->social_reason.' ha actualizado las calificaciones de su Lista de estándares mínimos, en el siguiente botón puede descargar los resultados')
            ->subcopy('Este link es valido por 24 horas')
            ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
            ->module('contracts')
            ->event('Job: ListCheckContractExportJob')
            ->company($this->company_id)
            ->send();
        }
    }
}
