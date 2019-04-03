<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class EvaluationContract extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_evaluation_contract';

    protected $fillable = [
        'evaluation_date',
        'evaluation_id',
        'contract_id',
        'company_id'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }

    public function contract()
    {
        return $this->belongsTo(ContractLessee::class, 'contract_id');
    }

    public function evaluators()
    {
        return $this->belongsToMany('App\User', 'sau_ct_evaluation_user', 'evaluation_id');
    }

    public function interviewees()
    {
        return $this->hasMany(Interviewee::class, 'evaluation_id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'evaluation_id');
    }

    public function results()
    {
        return $this->hasMany(EvaluationItemRating::class, 'evaluation_id');
    }
}