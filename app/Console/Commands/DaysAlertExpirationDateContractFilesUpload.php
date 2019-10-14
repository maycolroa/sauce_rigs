<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationsCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Traits\ContractTrait;
use Carbon\Carbon;

class DaysAlertExpirationDateContractFilesUpload extends Command
{
    use ContractTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'days-alert-expiration-date-contract-files-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos de alerta cuando la fecha de vencimiento de los archivos cargados en el módulo de contratistas está cercana según la configuración de los días';

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
        $companies = ConfigurationsCompany::findAllCompanyByKey('days_alert_expiration_date_contract_file_upload');

        foreach ($companies as $key => $value)
        {
            $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.*',
                    'sau_users.name as user_name'
                )
                ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
                ->whereRaw("CURDATE() = DATE_FORMAT(DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$value['value']." DAY), '%Y-%m-%d')")
                ->groupBy('sau_ct_file_upload_contracts_leesse.id');
            
            $files->company_scope = $value['company_id'];
            $files = $files->get();

            if ($files->count() > 0)
            {
                $recipients = $this->getUsersMasterContract($value['company_id']);

                NotificationMail::
                    subject('Contratistas - Archivos Próximos a Vencerse')
                    ->view('LegalAspects.contract.filesUploadExpiration')
                    ->recipients($recipients)
                    ->message('Los siguientes archivos están próximos a vencerse: ')
                    ->module('contracts')
                    ->event('Tarea programada: DaysAlertExpirationDateContractFilesUpload')
                    ->table($this->prepareDataTable($files))
                    ->company($value['company_id'])
                    ->send();
            }
        }
    }

    private function prepareDataTable($data)
    {
        $result = [];

        foreach ($data as $file) 
        {
            array_push($result, [
                'Nombre' => $file->name,
                'Fecha Vencimiento' => ($file->expirationDate) ? Carbon::createFromFormat('Y-m-d', $file->expirationDate)->toFormattedDateString() : '',
                'Usuario Creador' => $file->user_name,
                'Fecha Creación' => Carbon::createFromFormat('Y-m-d H:i:s', $file->created_at)->toFormattedDateString(),
                'Fecha Actualización' => Carbon::createFromFormat('Y-m-d H:i:s', $file->updated_at)->toFormattedDateString()
            ]);
        }
        
        return $result;
    }
}
