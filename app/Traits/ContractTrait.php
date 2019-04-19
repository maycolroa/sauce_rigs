<?php

namespace App\Traits;

use App\Models\LegalAspects\Contracts\ContractLessee;
use App\Models\Administrative\Users\User;

trait ContractTrait
{
    public function getUsersContract($contract_id, $company_id = null)
    {
        if (!is_numeric($contract_id))
            throw new \Exception('Contract invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLessee::select(
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
            $users = User::whereIn('id', $users_id)->get();
        }

        return $users;
    }

    public function getContractIdUser($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLessee::select(
                'sau_ct_information_contract_lessee.id AS id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id);

        if ($company_id)
            $contract->company_scope = $company_id;

        $contract = $contract->first();

        return $contract ? $contract->id : NULL;
    }

    public function getUsersMasterContract($company_id)
    {
        if (!is_numeric($company_id))
            throw new \Exception('Company invalid');
            
        $users = User::
              join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->leftJoin('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
            ->where('sau_company_user.company_id', $company_id)
            ->whereNull('sau_user_information_contract_lessee.information_id')
            ->get();

        return $users;
    }
}