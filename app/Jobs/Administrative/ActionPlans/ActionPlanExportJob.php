<?php

namespace App\Jobs\Administrative\ActionPlans;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Administrative\ActionPlans\ActionPlanExcel;
use App\Facades\Mail\Facades\NotificationMail;

class ActionPlanExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $filters;
    protected $isSuperAdmin;
    protected $all_permissions;
    protected $isContract;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $filters, $isSuperAdmin, $all_permissions, $isContract)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->isSuperAdmin = $isSuperAdmin;
      $this->all_permissions = $all_permissions;
      $this->isContract = $isContract;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/planes_de_accion_'.date("YmdHis").'.xlsx';
      Excel::store(new ActionPlanExcel($this->user, $this->company_id, $this->filters, $this->isSuperAdmin, $this->all_permissions, $this->isContract),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de los planes de acción')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de planes de acción.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('actionPlans')
        ->event('Job: ActionPlanExportJob')
        ->send();
    }
}
