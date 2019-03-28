<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class UserContractLesseeDetail extends Model
{
    protected $table = 'sau_user_information_contract_lessee';

    public function users(){
        return $this->belongsToMany('App\User', 'sau_users');
    }
}
