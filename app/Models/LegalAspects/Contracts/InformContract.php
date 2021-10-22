<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class InformContract extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_inform_contract';

    protected $fillable = [
        'inform_date',
        'inform_id',
        'company_id',
        'contract_id',
        'evaluator_id',
        'year',
        'month',
        'state',
        'observation',
        'Objective_inform'
    ];

    public function inform()
    {
        return $this->belongsTo(Inform::class, 'inform_id');
    }

    public function items()
    {
        return $this->hasMany(InformContractItem::class, 'inform_id');
    }

    public function contract()
    {
        return $this->belongsTo(ContractLesseeInformation::class, 'contract_id');
    }

    public function evaluator()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'evaluator_id');
    }

    public function observations()
    {
        return $this->hasMany(InformContractItemObservation::class, 'inform_id');
    }

    public function files()
    {
        return $this->hasMany(InformContractItemFile::class, 'inform_id');
    }

    public function results()
    {
        return $this->hasMany(InformContractItem::class, 'inform_id');
    }

    public function ready()
    {
        return $this->state == 'Terminada' ? true : false;
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