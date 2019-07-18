<?php

namespace App\Jobs\LegalAspects\Contracts\Evaluations;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationContractNotificationExcel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\Administrative\Users\User;

class EvaluationSendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;
    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id, $id)
    {
      $this->company_id = $company_id;
      $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $evaluationContract = EvaluationContract::select('sau_user_information_contract_lessee.user_id')
        ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_evaluation_contract.contract_id')
        ->where('sau_ct_evaluation_contract.id', $this->id);

      $evaluationContract->company_scope = $this->company_id;
      $evaluationContract = $evaluationContract->get();

      if ($evaluationContract)
      {
        $recipients = $evaluationContract->toArray();
        $recipients = User::active()->whereIn('id', $recipients)->get();
        
        if (!empty($recipients))
        {
          $nameExcel = 'export/1/evaluaciones_resultados_'.date("YmdHis").'.xlsx';
          Excel::store(new EvaluationContractNotificationExcel($this->company_id, $this->id),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
          
          $paramUrl = base64_encode($nameExcel);
          
          NotificationMail::
            subject('Resultados de evaluación')
            ->recipients($recipients)
            ->message('Se le ha realizado una evaluación por parte de su contratante, en el siguiente botón puede descargar los resultados')
            ->subcopy('Este link es valido por 24 horas')
            ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
            ->module('contracts')
            ->event('Job: EvaluationSendNotificationJob')
            ->company($this->company_id)
            ->send();
        }
      }
    }
}
