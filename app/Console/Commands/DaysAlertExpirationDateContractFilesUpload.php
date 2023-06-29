<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
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
            $company_id = $value['company_id'];

            $contracts = ContractLesseeInformation::where('company_id', $company_id)
            ->withoutGlobalScopes()->isActive()->get();

            foreach ($contracts as $key => $contract) 
            {
                if ($value['value'])
                {
                    $files_contracts = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_information_contract_lessee.id as contract_id'
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$value['value']." DAY)")
                        ->where('sau_ct_information_contract_lessee.id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_information_contract_lessee.id');

                    $files_globals = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_file_document_contract.contract_id as contract_id'
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_document_contract','sau_ct_file_document_contract.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$value['value']." DAY)")
                        ->where('sau_ct_file_document_contract.contract_id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_contract.contract_id');
                    
                    $files_employees = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_contract_employees.contract_id as contract_id'
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_document_employee','sau_ct_file_document_employee.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_contract_employees','sau_ct_contract_employees.id','sau_ct_file_document_employee.employee_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$value['value']." DAY)")
                        ->where('sau_ct_contract_employees.contract_id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_contract_employees.contract_id');

                    $files_list_check = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_list_check_qualifications.contract_id as contract_id'
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_item_contract','sau_ct_file_item_contract.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_list_check_qualifications','sau_ct_list_check_qualifications.id','sau_ct_file_item_contract.list_qualification_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$value['value']." DAY)")
                        ->where('sau_ct_list_check_qualifications.contract_id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_list_check_qualifications.contract_id');

                    $files_contracts->union($files_globals);
                    $files_contracts->union($files_employees);
                    $files_contracts->union($files_list_check);
                    $files_contracts = $files_contracts->get();

                    if ($files_contracts->count() > 0)
                    {
                        $recipients = $this->getUsersContract($contract->id, $company_id);

                        $responsibles = $contract->responsibles;

                        foreach ($responsibles as $key => $value) 
                        {
                            $recipients->push($value);
                        }

                        $recipients = $recipients->filter(function ($recipient, $index) use ($company_id) {
                            return $recipient->can('contracts_receive_notifications', $company_id) && !$recipient->isSuperAdmin($company_id);
                        });

                        if (!$recipients->isEmpty())
                        {
                            NotificationMail::
                                subject('Contratistas - Archivos Próximos a Vencerse')
                                ->view('LegalAspects.contract.filesUploadExpiration')
                                ->recipients($recipients)
                                ->message('Los siguientes archivos están próximos a vencerse: ')
                                ->module('contracts')
                                ->event('Tarea programada: DaysAlertExpirationDateContractFilesUpload')
                                ->table($this->prepareDataTable($files_contracts))
                                ->company($company_id)
                                ->send();
                        }
                    }
                }
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
