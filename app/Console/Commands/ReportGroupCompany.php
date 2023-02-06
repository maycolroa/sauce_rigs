<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\MonitorReportView;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\FileUpload;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\LegalAspects\Contracts\FileUpload AS FileContract;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use App\Models\System\CustomerMonitoring\NotificationScheduled;
use App\Models\System\CustomerMonitoring\Notification;
use App\Models\System\LogMails\LogMail;
use App\Models\General\CompanyGroup;
use App\Models\Administrative\Users\User;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\System\CompanyGroup\ReportExcel;
use DB;

class ReportGroupCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report-group-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de reporte de actividad de las compañias perteneciente a un grupo de compañia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groups = CompanyGroup::get();

        foreach ($groups as $key => $group) 
        {
            if ($group->receive_report == 'SI')
            {
                $nameExcel = 'export/1/reportCompanies'.date("YmdHis").'.xlsx';
                Excel::store(new ReportExcel($group),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
                
                $paramUrl = base64_encode($nameExcel);

                $recipients = User::where('id', -1)->get();
                $emails = explode(",", $group->emails);

                foreach ($emails as $key => $value)
                {
                    $recipients->push(new User(['email'=>$value]));
                }
            
                NotificationMail::
                    subject('Resumen de actividad de compañias')
                    ->recipients($recipients)
                    ->message('Se ha generado una exportación.')
                    ->subcopy('Este link es valido por 24 horas')
                    ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                    ->module('users')
                    ->event('ReportGroupCompany')
                    ->company(1)
                    ->send();
            }
        }
    }
}
