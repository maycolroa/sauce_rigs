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
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\FileUpload;

class ListCheckCopyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ContractTrait;

    protected $company_id;
    protected $contract;
    protected $contract_select;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $contract, $contract_select)
    {
      $this->company_id = $company_id;
      $this->contract = $contract;
      $this->contract_select = $contract_select;
      $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipients = User::select('sau_users.email')
            ->active()
            ->whereIn('sau_users.id', $this->user)
            ->groupBy('sau_users.id', 'sau_users.email');

        $recipients->company_scope = $this->company_id;
        $recipients = $recipients->get();

        /*$recipients = $recipients->filter(function ($recipient, $index) {
          return $recipient->can('contracts_receive_notifications', $this->company_id);
        });*/

        \Log::info($recipients);

        $qualifications = ItemQualificationContractDetail::select('item_id','qualification_id','observations')
          ->where('contract_id', $this->contract_select)
          ->get();

        \Log::info($qualifications);

        foreach ($qualifications as $qualification) 
        {
          $new_qualification = new ItemQualificationContractDetail;

          $new_qualification->item_id = $qualification->item_id;
          $new_qualification->qualification_id = $qualification->qualification_id;
          $new_qualification->observations = $qualification->observations;
          $new_qualification->contract_id = $this->contract;
          $new_qualification->save();
        }
        
        
        /*if (!$recipients->isEmpty())
        {          
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
        }*/
    }
}
