<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\InformContract;
use App\Models\LegalAspects\Contracts\InformContractItemFile;
use App\Models\LegalAspects\Contracts\InformContractItem;
use App\Models\LegalAspects\Contracts\TrainingEmployeeAttempt;
use App\Models\LegalAspects\Contracts\TrainingEmployeeQuestionsAnswers;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\Administrative\ActionPlans\ActionPlansFileEvidence;
use App\Models\Administrative\ActionPlans\ActionPlansTracing;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\EppTransfer;
use App\Models\IndustrialSecure\Epp\EppTransferDetail;
use App\Models\IndustrialSecure\Epp\EppReception;
use App\Models\IndustrialSecure\Epp\EppReceptionDetail;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionFirm;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\QualificationRepeat;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFieldsValues;
use DB;

class MigracionMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try
        {
            DB::beginTransaction();

            /*$old = DB::connection('mysql_old')->table('sau_ct_information_contract_lessee')->whereDate('created_at', '2024-10-29')->get();

            \Log::info("Creando contratistas");

            foreach ($old as $item)
            {
                $item = (array)$item;

                \Log::info($item['nit']);

                $idOld = $item['id'];

                unset($item['id']);

                $new = ContractLesseeInformation::withoutGlobalScopes()->firstOrCreate([
                    'nit' => $item['nit']
                ], $item);

                
                $oldRelation = DB::connection('mysql_old')->table('sau_ct_contracts_proyects')->where('contract_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    foreach ($oldRelation as $newContract)
                    {
                        DB::table('sau_ct_contracts_proyects')->updateOrInsert([
                            'contract_id' => $new->id,
                            'proyect_id' => $newContract->proyect_id
                        ]);
                    }
                }

                $oldRelation = DB::connection('mysql_old')->table('sau_ct_contracts_activities')->where('contract_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    foreach ($oldRelation as $newContract)
                    {
                        DB::table('sau_ct_contracts_activities')->updateOrInsert([
                            'contract_id' => $new->id,
                            'activity_id' => $newContract->activity_id
                        ]);
                    }
                }

                $oldRelation = DB::connection('mysql_old')->table('sau_ct_contract_responsibles')->where('contract_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    foreach ($oldRelation as $newContract)
                    {
                        DB::table('sau_ct_contract_responsibles')->updateOrInsert([
                            'contract_id' => $new->id,
                            'user_id' => $newContract->user_id
                        ]);
                    }
                }

                $oldRelation = DB::connection('mysql_old')->table('sau_ct_contract_high_risk_type')->where('contract_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    foreach ($oldRelation as $newContract)
                    {
                        DB::table('sau_ct_contract_high_risk_type')->updateOrInsert([
                            'contract_id' => $new->id,
                            'high_risk_type_id' => $newContract->high_risk_type_id
                        ]);
                    }
                }
            }

            \Log::info("------------------------- Creando contratistas");

            \Log::info("Creando Usuarios");

            $old = DB::connection('mysql_old')->table('sau_users')->whereDate('created_at', '2024-10-29')->get();

            foreach ($old as $item)
            {
                $item = (array)$item;

                \Log::info($item['email']);

                $idOld = $item['id'];

                unset($item['id']);

                $new = User::withoutGlobalScopes()->firstOrCreate([
                    'email' => $item['email']
                ], $item) ;

                \Log::info("Relacion contratista usuario");    

                $oldRelation = DB::connection('mysql_old')->table('sau_user_information_contract_lessee')->where('user_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    $oldContract = DB::connection('mysql_old')->table('sau_ct_information_contract_lessee')->whereIn('id', $oldRelation->pluck('information_id'))->get();

                    if ($oldContract->count() > 0)
                    {
                        $newContracts = ContractLesseeInformation::withoutGlobalScopes()->whereIn('nit', $oldContract->pluck('nit'))->pluck('id');

                        foreach ($newContracts as $newContract)
                        {
                            DB::table('sau_user_information_contract_lessee')->updateOrInsert([
                                'user_id' => $new->id,
                                'information_id' => $newContract
                            ]);
                        }
                    }
                }

                \Log::info("------------------------ Relacion contratista usuario");

                \Log::info("Relacion empresa usuario");  

                $oldRelation = DB::connection('mysql_old')->table('sau_company_user')->where('user_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    foreach ($oldRelation as $newContract)
                    {
                        DB::table('sau_company_user')->updateOrInsert([
                            'user_id' => $new->id,
                            'company_id' => $newContract->company_id
                        ]);
                    }
                }

                \Log::info("Relacion rol usuario");  

                $oldRelation = DB::connection('mysql_old')->table('sau_role_user')->where('user_id', $idOld)->get();

                if ($oldRelation->count() > 0)
                {
                    foreach ($oldRelation as $newContract)
                    {
                        DB::table('sau_role_user')->updateOrInsert([
                            'user_id' => $new->id,
                            'role_id' => $newContract->role_id,
                            'user_type' => $newContract->user_type,
                            'team_id' => $newContract->team_id
                        ]);
                    }
                }

                \Log::info("----------------------------- Relacion rol usuario");  
            }

            \Log::info("sau_ct_contract_employees");

            $old = DB::connection('mysql_old')->table('sau_ct_contract_employees')->where(function ($q) {
                $q->whereDate('created_at', '2024-10-29');
                $q->orWhereDate('created_at', '2024-10-30');
            })
            ->get();

            foreach ($old as $item)
            {
                $item = (array)$item;

                \Log::info($item['identification']);

                $oldContract = DB::connection('mysql_old')->table('sau_ct_information_contract_lessee')->where('id', $item['contract_id'])->first();

                if ($oldContract)
                {
                    $newContract = ContractLesseeInformation::withoutGlobalScopes()->where('nit', $oldContract->nit)->first();

                    if ($newContract)
                    {
                        $idOld = $item['id'];

                        unset($item['id']);

                        $item['contract_id'] = $newContract->id;

                        $new = ContractEmployee::withoutGlobalScopes()->firstOrCreate([
                            'identification' => $item['identification'],
                            'contract_id' => $newContract->id
                        ], $item) ;

                        $oldRelation = DB::connection('mysql_old')->table('sau_ct_contract_employee_proyects')->where('employee_id', $idOld)->get();

                        if ($oldRelation->count() > 0)
                        {
                            foreach ($oldRelation as $newContract)
                            {
                                DB::table('sau_ct_contract_employee_proyects')->updateOrInsert([
                                    'employee_id' => $new->id,
                                    'proyect_contract_id' => $newContract->proyect_contract_id
                                ]);
                            }
                        }

                        $oldRelation = DB::connection('mysql_old')->table('sau_ct_contract_employee_activities')->where('employee_id', $idOld)->get();

                        if ($oldRelation->count() > 0)
                        {
                            foreach ($oldRelation as $newContract)
                            {
                                DB::table('sau_ct_contract_employee_activities')->updateOrInsert([
                                    'employee_id' => $new->id,
                                    'activity_contract_id' => $newContract->activity_contract_id
                                ]);
                            }
                        }
                    }
                }
            }

            \Log::info("----------------------------- sau_ct_contract_employees");

            \Log::info("sau_ct_inform_contract");

            $old = DB::connection('mysql_old')->table('sau_ct_inform_contract')->whereDate('created_at', '2024-10-29')->get();

            foreach ($old as $item)
            {
                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = InformContract::withoutGlobalScopes()->firstOrCreate([
                    'inform_date' => $item['inform_date'],
                    'inform_id' => $item['inform_id'],
                    'company_id' => $item['company_id'],
                    'contract_id' => $item['contract_id'],
                    'proyect_id' => $item['proyect_id'],
                    'evaluator_id' => $item['evaluator_id']
                ], $item) ;

                $old2 = DB::connection('mysql_old')->table('sau_ct_inform_contract_item_files')->where('inform_id', $idOld)->whereDate('created_at', '2024-10-29')->get();

                foreach ($old2 as $item2)
                {
                    $item2 = (array)$item2;

                    unset($item2['id']);
                    $item2['inform_id'] = $new->id;

                    $new2 = InformContractItemFile::withoutGlobalScopes()->firstOrCreate([
                        'inform_id' => $item2['inform_id'],
                        'item_id' => $item2['item_id']
                    ], $item2) ;
                }

                $old2 = DB::connection('mysql_old')->table('sau_ct_inform_contract_items')->where('inform_id', $idOld)->get();

                foreach ($old2 as $item2)
                {
                    $item2 = (array)$item2;

                    unset($item2['id']);
                    $item2['inform_id'] = $new->id;

                    $new2 = InformContractItem::withoutGlobalScopes()->firstOrCreate([
                        'inform_id' => $item2['inform_id'],
                        'item_id' => $item2['item_id']
                    ], $item2) ;
                }
            }

            \Log::info("----------------------------- sau_ct_inform_contract");  

            \Log::info("sau_ct_training_employee_attempts");

            $old = DB::connection('mysql_old')->table('sau_ct_training_employee_attempts')->whereDate('created_at', '2024-10-29')->get();

            foreach ($old as $item)
            {
                $item = (array)$item;

                $idOld = $item['id'];

                $employeeOld = DB::connection('mysql_old')->table('sau_ct_contract_employees')->where('id', $item['employee_id'])->first();

                if ($employeeOld)
                {
                    $employeeOld = (array)$employeeOld;

                    $employeeNew = ContractEmployee::withoutGlobalScopes()->where('identification', $employeeOld['identification'])->first();

                    if ($employeeNew)
                    {
                        $item['employee_id'] = $employeeNew->id;

                        $new = TrainingEmployeeAttempt::withoutGlobalScopes()->firstOrCreate([
                            'attempt' => $item['attempt'],
                            'training_id' => $item['training_id'],
                            'employee_id' => $employeeNew->id
                        ], $item) ;

                        $old2 = DB::connection('mysql_old')->table('sau_ct_training_employee_questions_answers')->where('attempt_id', $idOld)->get();

                        foreach ($old2 as $item2)
                        {
                            $item2 = (array)$item2;

                            unset($item2['id']);
                            $item2['attempt_id'] = $new->id;

                            $new2 = TrainingEmployeeQuestionsAnswers::withoutGlobalScopes()->firstOrCreate([
                                'attempt_id' => $item2['attempt_id'],
                                'question_id' => $item2['question_id']
                            ], $item2) ;
                        }
                    }
                }
            }

            $old = DB::connection('mysql_old')->table('sau_ct_file_upload_contracts_leesse')->whereDate('created_at', '>=', '2024-10-29')->where('file', '<>', 'NDgyMDIwMjQtMTAtMzAgMTg6MDc6NDI4OTE4.pdf')->get();

            foreach ($old as $oldindex => $item)
            {
                \Log::info("sau_ct_file_upload_contracts_leesse: {$oldindex}");

                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = FileUpload::withoutGlobalScopes()->firstOrCreate([
                    'name' => $item['name'],
                    'file' => $item['file'],
                    'user_id' => $item['user_id']
                ], $item);
                
                /*$fcOld = DB::connection('mysql_old')->table('sau_ct_file_upload_contract')->where('file_upload_id', $idOld)->get();

                foreach ($fcOld as $fcOldRecord)
                {
                    $fcOldRecord = (array)$fcOldRecord;

                    $oldContract = DB::connection('mysql_old')->table('sau_ct_information_contract_lessee')->where('id', $fcOldRecord['contract_id'])->first();

                    if ($oldContract)
                    {
                        $newContract = ContractLesseeInformation::withoutGlobalScopes()->where('nit', $oldContract->nit)->first();

                        if ($newContract)
                        {
                            DB::table('sau_ct_file_upload_contract')->updateOrInsert([
                                'file_upload_id' => $new->id,
                                'contract_id' => $newContract->id
                            ]);
                        }
                    }    
                }

                $fcOld = DB::connection('mysql_old')->table('sau_ct_file_document_contract')->where('file_id', $idOld)->get();

                foreach ($fcOld as $fcOldRecord)
                {
                    $fcOldRecord = (array)$fcOldRecord;

                    $oldContract = DB::connection('mysql_old')->table('sau_ct_information_contract_lessee')->where('id', $fcOldRecord['contract_id'])->first();

                    if ($oldContract)
                    {
                        $newContract = ContractLesseeInformation::withoutGlobalScopes()->where('nit', $oldContract->nit)->first();

                        if ($newContract)
                        {
                            DB::table('sau_ct_file_document_contract')->updateOrInsert([
                                'file_id' => $new->id,
                                'contract_id' => $newContract->id,
                                'document_id' => $fcOldRecord['document_id']
                            ]);
                        }
                    }    
                }

                $fcOld = DB::connection('mysql_old')->table('sau_ct_file_document_employee')->where('file_id', $idOld)->get();

                foreach ($fcOld as $fcOldRecord)
                {
                    $fcOldRecord = (array)$fcOldRecord;

                    $oldContract = DB::connection('mysql_old')->table('sau_ct_contract_employees')->where('id', $fcOldRecord['employee_id'])->first();

                    if ($oldContract)
                    {
                        $newContract = ContractEmployee::withoutGlobalScopes()->where('identification', $oldContract->identification)->first();

                        if ($newContract)
                        {
                            DB::table('sau_ct_file_document_employee')->updateOrInsert([
                                'file_id' => $new->id,
                                'employee_id' => $newContract->id,
                                'document_id' => $fcOldRecord['document_id']
                            ]);
                        }
                    }    
                }

                $fcOld = DB::connection('mysql_old')->table('sau_ct_file_module_state')->where('file_id', $idOld)->whereDate('created_at', '>=', '2024-10-29')->get();

                foreach ($fcOld as $index => $fcOldRecord)
                {
                    $fcOldRecord = (array)$fcOldRecord;

                    $new2 = FileModuleState::withoutGlobalScopes()->firstOrCreate([
                        'contract_id' => $fcOldRecord['contract_id'],
                        'file_id' => $new->id,
                        'module' => $fcOldRecord['module'],
                        'date' => $fcOldRecord['date'],
                        'state' => $fcOldRecord['state']
                    ], $fcOldRecord) ;
                }
            }*/

            /////////////Epp Entregas inicio

            $oldEpp = DB::connection('mysql_old')->table('sau_epp_transactions_employees')->whereDate('created_at', '2024-10-29')->get();

            foreach ($oldEpp as $item)
            {
                $idsElmentsNew = [];

                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = ElementTransactionEmployee::withoutGlobalScopes()->insertOrCreate($item);

                $oldElements = DB::connection('mysql_old')->table('sau_epp_transaction_employee_element')->where('transaction_employee_id', $idOld)->get();

                foreach ($oldElements as $oldElement) 
                {
                    $eleOld = ElementBalanceSpecific::withoutGlobalScopes()->where('id', $oldElement['element_id'])->first();

                    if ($element)
                    {
                        if ($item['type'] == 'Entrega')
                        {
                            if ($element->state == 'Asignado')
                            {
                                $newElement = ElementBalanceSpecific::withoutGlobalScopes()->where('element_balance_id', $element->element_balance_id)->where('state', 'Disponible')->first();

                                if ($newElement)
                                {
                                    $newElement->state = 'Asignado';
                                    $newElement->save();

                                    array_push($idsElmentsNew, $newElement->id);
                                }
                            }
                            else
                            {
                                $element->state = 'Asignado';
                                $element->save();

                                array_push($idsElmentsNew, $element->id);
                            }
                        }
                        else
                        {
                            array_push($idsElmentsNew, $eleOld->id);
                        }
                    }
                }

                foreach ($idsElmentsNew as $idNew) 
                {
                    DB::table('sau_epp_transaction_employee_element')->updateOrInsert([
                        'transaction_employee_id' => $new->id,
                        'element_id' => $idNew
                    ]);
                }
            }

            /////////////Epp Entregas final

            /////////////Epp Transferencias - Recepciones inicio

            $oldTransfer = DB::connection('mysql_old')->table('sau_epp_transfers')->whereDate('created_at', '2024-10-29')->get();

            foreach ($oldTransfer as $item)
            {
                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = EppTransfer::withoutGlobalScopes()->insertOrCreate($item);

                $oldReception = DB::connection('mysql_old')->table('sau_epp_receptions')->where('transfer_id', $idOld)->first();

                $oldReception = (array)$oldReception;

                $idOldRecep = $oldReception['id'];

                unset($oldReception['id']);

                $newReception = EppReception::withoutGlobalScopes()->insertOrCreate($oldReception);

                $oldDetailsTransfer = DB::connection('mysql_old')->table('sau_epp_transfer_details')->where('transfer_id', $idOld)->get();

                foreach ($oldDetailsTransfer as $oldDetail) 
                {
                    $idsElmentsNew = [];

                    $oldDetail = (array)$oldDetail;

                    $idOldDetTrans = $oldDetail['id'];

                    unset($oldDetail['id']);

                    $newDetails = EppTransferDetail::withoutGlobalScopes()->insertOrCreate($oldDetail);

                    $oldDetailReception = DB::connection('mysql_old')->table('sau_epp_receptions_details')->where('reception_id', $idOldRecep)->where('element_id', $oldDetail['element_id'])->where('location_id', $oldDetail['location_origin_id'])->where('location_id', $oldDetail['location_destiny_id'])->first();

                    $oldDetailRecep = (array)$oldDetailRecep;

                    $idOldDetRecep = $oldDetailRecep['id'];

                    unset($oldDetailRecep['id']);

                    $newDetailsRecep = EppReceptionDetail::withoutGlobalScopes()->insertOrCreate($oldDetail);

                    $old_balance_location = ElementBalanceLocation::where('element_id', $oldDetail['element_id'])->where('location_id', $oldDetail['location_origin_id'])->first();
                    $old_balance_location->quantity = $old_balance_location->quantity - $oldDetail['quantity'];
                    $old_balance_location->quantity_available = $old_balance_location->quantity_available - $oldDetail['quantity'];
                    $old_balance_location->save();

                    $new_balance_location = ElementBalanceLocation::where('element_id', $oldDetail['element_id'])->where('location_id', $oldDetail['location_destiny_id'])->first();
                    $new_balance_location->quantity = $new_balance_location->quantity + $oldDetail['quantity'];
                    $new_balance_location->quantity_available = $new_balance_location->quantity_available + $oldDetail['quantity'];
                    $new_balance_location->save();

                    $elements = DB::connection('mysql_old')->table('sau_epp_transfer_details_element')->where('transfer_detail_id', $oldDetail['id'])->get();

                    foreach ($elements as $element) 
                    {
                        $element = (array)$element;

                        $eleOld = ElementBalanceSpecific::withoutGlobalScopes()->where('id', $element['element_specific_id'])->where('element_balance_id', $old_balance_location->id)->first();

                        if ($eleOld)
                        {
                            if ($eleOld->state == 'Asignado')
                            {
                                $newElement = ElementBalanceSpecific::withoutGlobalScopes()->where('element_balance_id', $eleOld->element_balance_id)->where('state', 'Disponible')->first();

                                if ($newElement)
                                {
                                    $newElement->element_balance_id = $new_balance_location->id;
                                    $newElement->save();

                                    array_push($idsElmentsNew, $newElement->id);
                                }
                            }
                            else
                            {
                                $eleOld->element_balance_id = $new_balance_location->id;
                                $eleOld->save();

                                array_push($idsElmentsNew, $element->id);
                            }
                        }
                        else
                        {

                            $newElement = ElementBalanceSpecific::withoutGlobalScopes()->where('element_balance_id', $element['element_balance_id'])->where('state', 'Disponible')->first();

                            if ($newElement)
                            {
                                $newElement->element_balance_id = $new_balance_location->id;
                                $newElement->save();

                                array_push($idsElmentsNew, $newElement->id);
                            }
                        }
                    }

                    foreach ($idsElmentsNew as $idNew) 
                    {
                        DB::table('sau_epp_transfer_details_element')->updateOrInsert([
                            'transfer_detail_id' => $newDetails->id,
                            'element_specific_id' => $idNew
                        ]);


                        DB::table('sau_epp_receptions_details_elements')->updateOrInsert([
                            'reception_detail_id' => $newDetailsRecep->id,
                            'element_specific_ids' => $idNew
                        ]);


                        DB::table('sau_epp_receptions_details_elements_received')->updateOrInsert([
                            'reception_detail_id' => $newDetailsRecep->id,
                            'element_specific_ids' => $idNew
                        ]);
                    }
                }
            }

            /*$oldImgApi = DB::connection('mysql_old')->table('sau_ph_images_api')->whereDate('created_at', '2024-10-29')->get();

            foreach ($oldImgApi as $item)
            {
                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = ImageApi::withoutGlobalScopes()->firstOrCreate([
                    'file' => $item['file'],
                    'hash' => $item['hash'],
                    'type' => $item['type']
                ],
                $item);
            }

            $oldPhReport = DB::connection('mysql_old')->table('sau_ph_reports')->whereDate('created_at', '2024-10-29')->get();

            foreach ($oldPhReport as $item)
            {
                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = Report::withoutGlobalScopes()->firstOrCreate([
                    'condition_id' => $item['condition_id'],
                    'employee_regional_id' => $item['employee_regional_id'],
                    'employee_headquarter_id' => $item['employee_headquarter_id'],
                    'employee_process_id' => $item['employee_process_id'],
                    'employee_area_id' => $item['employee_area_id'],
                    'rate' => $item['rate'],
                    'image_1' => $item['image_1'],
                    'image_2' => $item['image_2'],
                    'image_3' => $item['image_3']
                ], $item);

                $oldActionPlan = DB::connection('mysql_old')->table('sau_action_plans_activity_module')->where('item_id', $idOld)->where('item_table_name', DB::raw("'sau_ph_reports'"))->get();

                if ($oldActionPlan->count() > 0)
                {
                    foreach ($oldActionPlan as $oldApm) 
                    {
                        $oldApm = (array)$oldApm;

                        $oldAp = DB::connection('mysql_old')->table('sau_action_plans_activities')->where('id', $value['activity_id'])->get();

                        $oldAp = (array)$oldAp;

                        $idOldAp = $oldAp['id'];

                        unset($oldAp['id']);

                        $newAp = ActionPlansActivity::withoutGlobalScopes()->firstOrCreate([
                            'description' => $oldAp['description'],
                            'responsible_id' => $oldAp['responsible_id'],
                            'execution_date' => $oldAp['execution_date'],
                            'state' => $oldAp['state'],
                            'company_id' => $oldAp['company_id'],
                            'detail_procedence' => $oldAp['detail_procedence']
                        ], $oldAp);

                        DB::table('sau_action_plans_activity_module')->updateOrInsert([
                            'item_table_name' => 'sau_ph_reports',
                            'activity_id' => $newAp->id,
                            'module' => $oldApm['module'],
                            'item_id' => $new->id
                        ]);

                        $filesAp =  DB::connection('mysql_old')->table('sau_action_plans_files_evidences')->where('activity_id', $idOldAp)->get();

                        if ($filesAp->count() > 0)
                        {
                            foreach ($filesAp as $file) 
                            {
                                $file = (array)$file;
        
                                unset($file['id']);

                                $file['activity_id'] = $newAp->id;

                                $fileNew = ActionPlansFileEvidence::withoutGlobalScopes()->firstOrCreate([
                                    'activity_id' => $file['activity_id'],
                                    'file' => $file['file'],
                                    'file_name' => $file['file_name']
                                ], $file);
                            }
                        }
                    }

                }
            }


            $oldPhQualification = DB::connection('mysql_old')->table('sau_ph_inspection_items_qualification_area_location')->whereDate('qualification_date', '2024-10-29')->get();

            foreach ($oldPhQualification as $item)
            {
                $item = (array)$item;

                $idOld = $item['id'];

                unset($item['id']);

                $new = InspectionItemsQualificationAreaLocation::withoutGlobalScopes()->firstOrCreate([
                    'employee_regional_id' => $item['employee_regional_id'],
                    'employee_headquarter_id' => $item['employee_headquarter_id'],
                    'employee_process_id' => $item['employee_process_id'],
                    'employee_area_id' => $item['employee_area_id'],
                    'qualification_date' => $item['qualification_date'],
                    'photo_1' => $item['photo_1'],
                    'photo_2' => $item['photo_2']
                ], $item);
            }*/

            DB::commit();

        } catch (\Exception $e) {
            $errors = "{$e->getMessage()} \n {$e->getTraceAsString()}";
            \Log::error($errors);
            DB::rollback();
        }
    }
}