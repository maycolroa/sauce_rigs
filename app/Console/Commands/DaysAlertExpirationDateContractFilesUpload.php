<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Traits\ContractTrait;
use Carbon\Carbon;
use App\Models\System\Licenses\License;
use App\Models\Administrative\Users\User;
use DB;

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
        $date = Carbon::now()->subDay()->format('Y-m-d');

        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $value)
        {
            $company_id = $value;

            $configDay = $this->getConfig($company_id);

            if (!$configDay)
                continue;

            $contracts = ContractLesseeInformation::where('company_id', $company_id)
            ->withoutGlobalScopes()->isActive()->get();

            foreach ($contracts as $key => $contract) 
            {
                if ($configDay)
                {
                    //$configDay = 17;
                    $files_contracts = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.id as id',
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_information_contract_lessee.id as contract_id',
                            'sau_ct_information_contract_lessee.social_reason as social_reason',
                            DB::raw("'' as employee_name")
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
                        ->join('sau_ct_file_document_employee','sau_ct_file_document_employee.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_contract_employees','sau_ct_contract_employees.id','sau_ct_file_document_employee.employee_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$configDay." DAY)")
                        ->where('sau_ct_information_contract_lessee.id', $contract->id)
                        ->whereNULL('sau_ct_contract_employees.id')
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_information_contract_lessee.id');

                    $files_globals = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.id as id',
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_file_document_contract.contract_id as contract_id',
                            'sau_ct_information_contract_lessee.social_reason as social_reason',
                            DB::raw("'' as employee_name")
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_document_contract','sau_ct_file_document_contract.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_document_contract.contract_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$configDay." DAY)")
                        ->where('sau_ct_file_document_contract.contract_id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_information_contract_lessee.id');
                    
                    $files_employees = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.id as id',
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_contract_employees.contract_id as contract_id',
                            'sau_ct_information_contract_lessee.social_reason as social_reason',
                            "sau_ct_contract_employees.name as employee_name"
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_document_employee','sau_ct_file_document_employee.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_contract_employees','sau_ct_contract_employees.id','sau_ct_file_document_employee.employee_id')
                        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.contract_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$configDay." DAY)")
                        ->where('sau_ct_contract_employees.contract_id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.id');

                    $files_list_check = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.id as id',
                            'sau_ct_file_upload_contracts_leesse.name as name',
                            'sau_ct_file_upload_contracts_leesse.expirationDate as expirationDate',
                            'sau_ct_file_upload_contracts_leesse.created_at as created_at',
                            'sau_ct_file_upload_contracts_leesse.updated_at as updated_at',
                            'sau_users.name as user_name',
                            'sau_ct_list_check_qualifications.contract_id as contract_id',
                            'sau_ct_information_contract_lessee.social_reason as social_reason',
                            DB::raw("'' as employee_name")
                        )
                        ->withoutGlobalScopes()
                        ->join('sau_users','sau_users.id','sau_ct_file_upload_contracts_leesse.user_id')
                        ->join('sau_ct_file_item_contract','sau_ct_file_item_contract.file_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_list_check_qualifications','sau_ct_list_check_qualifications.id','sau_ct_file_item_contract.list_qualification_id')
                        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_list_check_qualifications.contract_id')
                        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -".$configDay." DAY)")
                        ->where('sau_ct_list_check_qualifications.contract_id', $contract->id)
                        ->groupBy('sau_ct_file_upload_contracts_leesse.id', 'sau_ct_information_contract_lessee.id');

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

                        $notifyContractor = $this->getConfigNotify($company_id);
                        
                        if ($notifyContractor == 'SI')
                        {
                            $usersContractor = $this->getConfigNotifyUser($company_id);

                            foreach ($usersContractor as $key => $user) 
                            {
                                $usuario = User::find($user['value']);
                                $recipients->push($usuario);
                            }
                        }

                        $recipients = $recipients->filter(function ($recipient, $index) use ($company_id) {
                            try {
                                if ($recipient)
                                    return $recipient->can('contracts_receive_notifications', $company_id) && !$recipient->isSuperAdmin($company_id);
                            } catch (\Exception $e) {
                                \Log::info($e->getMessage());
                                return false;
                            }
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
                'Contratista' => $file->social_reason,
                'Empleado' => $file->employee_name,
                'Nombre' => $file->name,
                'Fecha Vencimiento' => ($file->expirationDate) ? Carbon::createFromFormat('Y-m-d', $file->expirationDate)->toFormattedDateString() : '',
                'Usuario Creador' => $file->user_name,
                'Fecha Creación' => Carbon::createFromFormat('Y-m-d H:i:s', $file->created_at)->toFormattedDateString(),
                'Fecha Actualización' => Carbon::createFromFormat('Y-m-d H:i:s', $file->updated_at)->toFormattedDateString()
            ]);
        }
        
        return $result;
    }

    public function getConfig($company_id)
    {
        $key = 'days_alert_expiration_date_contract_file_upload';

        try
        {
            $days = ConfigurationsCompany::company($company_id)->findByKey($key);
            
            if ($days)
                return $days;
            else
                return NULL;
            
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return null;
        }
    }

    public function getConfigNotify($company_id)
    {
        $key = 'contract_notify_file_expired';

        try
        {
            $days = ConfigurationsCompany::company($company_id)->findByKey($key);
            
            if ($days)
                return $days;
            else
                return NULL;
            
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return null;
        }
    }

    public function getConfigNotifyUser($company_id)
    {
        $key = 'contract_notify_file_expired_user';

        try
        {
            $value = ConfigurationsCompany::company($company_id)->findByKey($key);

            $values = explode(',', $value);

            $users = [];

            foreach ($values as $email) 
            {
                $user = User::where('email', $email)->first();

                if ($user)
                    array_push($users, $user->multiselect());
            }
            if (COUNT($users) > 0)
                return $users;
            else
                return NULL;
            
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return null;
        }
    }
}
