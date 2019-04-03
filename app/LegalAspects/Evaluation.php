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
        'company_id',
        'creator_user_id'
    ];

    public function ratingsTypes()
    {
        return $this->belongsToMany(TypeRating::class, 'sau_ct_evaluation_type_rating');
    }

    public function objectives()
    {
        return $this->hasMany(Objective::class, 'evaluation_id');
    }

    public function evaluationContracts()
    {
        return $this->hasMany(EvaluationContract::class, 'evaluation_id');
    }
}