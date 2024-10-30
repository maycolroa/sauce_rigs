<?php

namespace App\Jobs\IndustrialSecure\Epp;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\Epp\DeliveryExportExcel;
use App\Facades\Mail\Facades\NotificationMail;

class DeliveryExportJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $user;
  protected $company_id;
  protected $filters;

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
    $nameExcel = 'export/1/epp_deliveries_'.date("YmdHis").'.xlsx';
    Excel::store(new DeliveryExportExcel($this->company_id, $this->filters),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
    
    $paramUrl = base64_encode($nameExcel);
    
    NotificationMail::
      subject('Exportación de las entregas de elementos de proteccion personal')
      ->recipients($this->user)
      ->message('Se ha generado una exportación de entregas.')
      ->subcopy('Este link es valido por 24 horas')
      ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
      ->module('epp')
      ->event('Job: DeliveryExportJob')
      ->company($this->company_id)
      ->send();
  }
}
