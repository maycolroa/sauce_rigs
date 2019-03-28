<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Evaluation extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_evaluations';

    protected $fillable = [
        'name',
        'type',
        'evaluation_date',
        'information_contract_lessee_id',
        'company_id',
        'creator_user_id'
    ];

    public function contract()
    {
        return $this->belongsTo(ContractLessee::class, 'information_contract_lessee_id');
    }

    public function evaluators()
    {
        return $this->belongsToMany('App\User', 'sau_ct_evaluation_user');
    }

    public function interviewees()
    {
        return $this->hasMany(Interviewee::class, 'evaluation_id');
    }

    public function objectives()
    {
        return $this->hasMany(Objective::class, 'evaluation_id');
    }
}