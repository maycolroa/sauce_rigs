<?php

namespace App\Traits;

use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\Administrative\Users\User;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use DB;
use Session;
//use App\Models\LegalAspects\Contracts\LiskCheckResumen;

trait ContractTrait
{
    public function getUsersContract($contract_id, $company_id = null, $scope_active = true)
    {
        if (!is_numeric($contract_id))
            throw new \Exception('Contract invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_user_information_contract_lessee.user_id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_ct_information_contract_lessee.id', $contract_id);

        if ($company_id)
            $contract->company_scope = $company_id;

        $contract = $contract->get();
        $users = collect([]);

        if ($contract)
        {
            $users_id = $contract->toArray();
            
            if ($scope_active)
                $users = User::whereIn('id', $users_id)->get();
            else
                $users = User::active()->whereIn('id', $users_id)->get();
        }

        return $users->unique();
    }

    public function getUserMasterContract($contract, $company_id = null, $scope_active = true)
    {
        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $user_master = NULL;

        if ($contract->user_sst_id)
        {
            $user_master = User::find($contract->user_sst_id);
        }
        else
        {
            $user_contract = ContractLesseeInformation::select(
                    'sau_user_information_contract_lessee.user_id')
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
                ->where('sau_ct_information_contract_lessee.id', $contract->id);

            if ($company_id)
                $user_contract->company_scope = $company_id;

            $user_contract = $user_contract->first();
            $users = collect([]);

            if ($user_contract)
            {                
                if ($scope_active)
                    $user_master = User::find($user_contract->user_id);
                else
                    $user_master = User::active()->find($user_contract->user_id);
            }
        }

        return $user_master;
    }

    public function getContractUserLogin($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.*'
            )
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id);

        if ($company_id)
        {
            $active_company = DB::table('sau_company_user')
                ->where('company_id', $company_id)
                ->where('user_id', $user_id)
                ->where('active', 'SI')
                ->first();

            if ($active_company)
                $contract->company_scope = $company_id;
            else
                return NULL;
        }

        $contract = $contract->first();

        return $contract ? $contract : NULL;
    }

    public function getContractUser($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        if (Session::get('contract_id'))
        {
            $contract = ContractLesseeInformation::select(
                    'sau_ct_information_contract_lessee.*'
                )
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
                ->where('sau_user_information_contract_lessee.user_id', $user_id)
                ->where('sau_ct_information_contract_lessee.id', Session::get('contract_id'));

            if ($company_id)
                $contract->company_scope = $company_id;

            $contract = $contract->first();

            return $contract ? $contract : NULL;
        }
        else
            return $this->getContractUserLogin($user_id);
    }


    public function getMultiplesContracstUser($user_id, $multiple = false, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.*'
            )
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id);

        if (!$multiple)
        {
            if ($company_id)
                $contract->company_scope = $company_id;
        }
        else
            $contract = $contract->withoutGlobalScopes();

        $contract = $contract->get();

        return $contract ? $contract : NULL;
    }

    public function getContractIdUser($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.id AS id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id)
            ->where('sau_ct_information_contract_lessee.id', Session::get('contract_id'));

        if ($company_id)
            $contract->company_scope = $company_id;

        $contract = $contract->first();

        return $contract ? $contract->id : NULL;
    }

    public function getUsersMasterContract($company_id = null)
    {
        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');
            
        $users = User::select('sau_users.*')
            ->active()
            //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->leftJoin('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
            ->leftJoin('sau_role_user', function($q) use ($company_id) { 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                ->on('sau_role_user.team_id', '=', DB::raw($company_id));
            })
            ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->whereNotIn('sau_roles.id', [8,9,5])
            ->whereNull('sau_user_information_contract_lessee.information_id')
            ->orderBy('name');
            //->get();

        if ($company_id)
            $users->company_scope = $company_id;

        $users = $users->get();

        return $users;
    }

    public function getStandardItemsContract($contract)
    {
        $sql = SectionCategoryItems::select(
            'sau_ct_section_category_items.*',
            'sau_ct_standard_classification.standard_name as name'
        )
        ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
        ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id');

        $items = [];

        if ($contract->classification == 'UPA')
        {
            if ($contract->number_workers <= 10)
            {
                if ($contract->risk_class == "Clase de riesgo I" || $contract->risk_class == "Clase de riesgo II" || $contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '3 estandares')->get();
                }
                else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
        }
        else if ($contract->classification == 'Empresa')
        {
            if ($contract->number_workers <= 10)
            {
                if ($contract->risk_class == "Clase de riesgo I" || $contract->risk_class == "Clase de riesgo II" || $contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '7 estandares')->get();
                }
                else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->number_workers > 10 && $contract->number_workers <= 50)
            {
                if ($contract->risk_class == "Clase de riesgo I" || $contract->risk_class == "Clase de riesgo II" || $contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '21 estandares')->get();
                }
                else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
            else if ($contract->number_workers > 50)
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();            
            }
        }

        return $items;
    }

    public function reloadLiskCheckResumen($contract, $qualification)
    {
        $items = [];
        $items_delete = [];
        
        if ($contract->type == 'Contratista' || $contract->type == 'Proveedor')
            $items = $this->getStandardItemsContract($contract);
        
        $items_delete = COUNT($items) > 0 ? $items->pluck('id') : [];

        $contract->listCheckResumen()->where('list_qualification_id', $qualification)->delete();

        $items_delete = ItemQualificationContractDetail::select(
                    'sau_ct_item_qualification_contract.*',
                    'sau_ct_qualifications.name AS name'
                )
                ->join('sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ct_item_qualification_contract.qualification_id')
                ->where('contract_id', $contract->id)
                ->where('list_qualification_id', $qualification)
                ->whereNotIn('item_id', $items_delete)
                ->get();

        foreach ($items_delete as $item)
        {
            if ($item->name == 'C')
            {
                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.file AS file'
                  )
                  ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                  ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                  ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                  ->where('sau_ct_file_item_contract.item_id', $item->item_id)
                  ->where('sau_ct_file_item_contract.list_qualification_id', $qualification)
                  ->get();
                
                foreach ($files as $file)
                {
                    FileUpload::find($file->id)->delete();
                    Storage::disk('s3')->delete('legalAspects/files/'. $file->file);
                }
            } 
            else if ($item->name == 'NC')
            {
                ActionPlan::model($item)->modelDeleteAll();
            }

            ItemQualificationContractDetail::find($item->id)->delete();
        }

        if (COUNT($items) > 0)
        {
            $totales = [
                'list_qualification_id' => $qualification,
                'total_standard' => 0,
                'total_c' => 0,
                'total_nc' => 0,
                'total_sc' => 0,
                'total_p_c' => 0,
                'total_p_nc' => 0
            ];

            $qualifications = Qualifications::pluck("name", "id");

            try
            {
                $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
                
            } catch (\Exception $e) {
                $exist = 'NO';
            }

            //Obtiene los items calificados
            $items_calificated = ItemQualificationContractDetail::
                      where('contract_id', $contract->id)
                    ->where('list_qualification_id', $qualification);                   

            if ($exist == 'SI')
                $items_calificated->where('state_aprove_qualification', 'APROBADA');

            $items_calificated = $items_calificated->pluck("qualification_id", "item_id");

            $items->each(function($item, $index) use ($qualifications, $items_calificated, $contract, &$totales) {
                
                $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                
                $totales['total_standard']++;

                if ($item->qualification == 'C' || $item->qualification == 'NA')
                {
                    $totales['total_c']++;
                }
                else if ($item->qualification == 'NC')
                {
                    $totales['total_nc']++;
                }
                else
                {
                    $totales['total_nc']++;
                    $totales['total_sc']++;
                }

                return $item;
            });

            $totales['total_p_c'] = round(($totales['total_c'] / $totales['total_standard']) * 100, 1);
            $totales['total_p_nc'] = round(($totales['total_nc'] / $totales['total_standard']) * 100, 1);

            $contract->listCheckResumen()->updateOrCreate(['contract_id'=>$contract->id, 'list_qualification_id' => $qualification], $totales);
        }
    }

    public function reloadLiskCheckResumenPercentege($contract, $qualification)
    {
        $contract->listCheckResumen()->where('list_qualification_id', $qualification)->delete();

        if ($contract->type == 'Contratista' || $contract->type == 'Proveedor')
            $items = $this->getStandardItemsContract($contract);
            
        if (COUNT($items) > 0)
        {
            $totales = [
                'list_qualification_id' => $qualification,
                'total_standard' => 0,
                'total_c' => 0,
                'total_nc' => 0,
                'total_sc' => 0,
                'total_p_c' => 0,
                'total_p_nc' => 0
            ];

            $qualifications = Qualifications::pluck("name", "id");

            try
            {
                $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
                
            } catch (\Exception $e) {
                $exist = 'NO';
            }

            //Obtiene los items calificados
            $items_calificated = ItemQualificationContractDetail::
                      where('contract_id', $contract->id)
                    ->where('list_qualification_id', $qualification);                   

            if ($exist == 'SI')
                $items_calificated->where('state_aprove_qualification', 'APROBADA');

            $items_calificated = $items_calificated->pluck("qualification_id", "item_id");

            $items->each(function($item, $index) use ($qualifications, $items_calificated, $contract, &$totales) {
                
                $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                
                $totales['total_standard']++;

                if ($item->qualification == 'C' || $item->qualification == 'NA')
                {
                    $totales['total_c']++;
                }
                else if ($item->qualification == 'NC')
                {
                    $totales['total_nc']++;
                }
                else
                {
                    $totales['total_nc']++;
                    $totales['total_sc']++;
                }

                return $item;
            });

            $totales['total_p_c'] = round(($totales['total_c'] / $totales['total_standard']) * 100, 1);
            $totales['total_p_nc'] = round(($totales['total_nc'] / $totales['total_standard']) * 100, 1);

            $contract->listCheckResumen()->updateOrCreate(['contract_id'=>$contract->id, 'list_qualification_id' => $qualification], $totales);
        }
    }
}