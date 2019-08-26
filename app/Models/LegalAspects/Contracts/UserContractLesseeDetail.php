<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class UserContractLesseeDetail extends Model
{
    protected $table = 'sau_user_information_contract_lessee';

    public function users(){
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_users');
    }
}
