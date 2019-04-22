<?php

namespace App\Models\LegalAspects\Contracts;

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
        'company_id',
        'evaluator_id'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }

    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }

    public function evaluators()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_ct_evaluation_user', 'evaluation_id');
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

    public function histories()
    {
        return $this->hasMany(EvaluationContractHistory::class, 'evaluation_id');
    }

    /**
     * filters checks through the given objectives
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $objectives
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInObjectives($query, $objectives, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($objectives as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_objectives.id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_objectives.id', $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given subobjectives
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $subobjectives
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInSubobjectives($query, $subobjectives, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($subobjectives as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_subobjectives.id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_subobjectives.id', $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_ct_evaluation_contract.evaluation_date', $dates);
            return $query;
        }
    }
}