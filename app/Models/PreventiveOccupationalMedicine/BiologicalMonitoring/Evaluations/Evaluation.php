<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Evaluation extends Model
{
    use CompanyTrait;

    protected $table = 'sau_bm_evaluations';

    protected $fillable = [
        'name',
        'company_id',
        'creator_user_id',
        'in_edit',
        'user_edit',
        'time_edit'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'time_edit',
    ];

    protected $casts = [
        'in_edit' => 'boolean'
    ];

    public function stages()
    {
        return $this->hasMany(Stage::class, 'evaluation_id');
    }

    /*public function evaluationContracts()
    {
        return $this->hasMany(EvaluationContract::class, 'evaluation_id');
    }*/

    /**
     * filters checks through the given objectives
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $objectives
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeInObjectives($query, $objectives, $typeSearch = 'IN')
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
    /*public function scopeInSubobjectives($query, $subobjectives, $typeSearch = 'IN')
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
     * filters checks through the given items
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $items
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeInItems($query, $items, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($items as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_items.id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_items.id', $ids);
        }

        return $query;
    }


    /**
     * filters checks through the given evaluations
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $evaluations
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeInEvaluations($query, $evaluations, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($evaluations as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_evaluations.id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_evaluations.id', $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given contracts
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $contracts
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeInContracts($query, $contracts, $typeSearch = 'IN')
    {
        if (COUNT($contracts) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_item_qualification_contract.contract_id', $contracts);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_item_qualification_contract.contract_id', $contracts);
        }

        return $query;
    }


    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_ct_evaluation_contract.evaluation_date', $dates);
            return $query;
        }
    }

    public function scopeLocked($query)
    {
        return $query->where('in_edit', true);
    }*/
}