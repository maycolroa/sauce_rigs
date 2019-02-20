<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class ContractLesseeInformation extends Model
{
    protected $table = 'sau_ct_information_contract_lessee';

    public function usersContractInformation(){
        return $this->hasMany('');
    }
}
