<?php

namespace App\Managers\System\Alerts;

use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\System\CustomerMonitoring\CustomerMonitoringExcel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\System\CustomerMonitoring\Notification;

class CustomerMonitoring
{
    protected $notification;
    /**
     * creates an instance and sets the class attributes
     * @param string $globalOption
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function send()
    {
        $nameExcel = 'monitoreo_de_clientes_'.date("YmdHis").'.xlsx';
        Excel::store(new CustomerMonitoringExcel(),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
        
        $paramUrl = base64_encode($nameExcel);
        
        NotificationMail::
          subject('Reportes Monitoreo de Clientes')
          ->recipients($this->notification->users)
          ->message('Se ha generado una exportación de Reportes Monitoreo de Clientes.')
          ->subcopy('Este link es valido por 24 horas')
          ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
          ->module('users')
          ->event('Alert: CustomerMonitoring')
          ->company(1)
          ->send();
    }
}