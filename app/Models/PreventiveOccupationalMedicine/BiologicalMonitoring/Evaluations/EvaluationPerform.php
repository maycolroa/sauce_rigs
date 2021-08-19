<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;

class EvaluationPerform extends Model
{
    protected $table = 'sau_bm_evaluation_perform';

    protected $fillable = [
        'evaluation_date',
        'evaluation_id',
        'company_id',
        'evaluator_id',
        'state',
        'observation'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }

    public function evaluators()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_bm_evaluation_evaluators', 'evaluation_id');
    }

    public function interviewees()
    {
        return $this->hasMany(Interviewee::class, 'evaluation_id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'evaluation_id');
    }

    public function files()
    {
        return $this->hasMany(EvaluationFile::class, 'evaluation_id');
    }

    public function results()
    {
        return $this->hasMany(EvaluationPerformItem::class, 'evaluation_id');
    }

    public function ready()
    {
        return $this->state == 'Terminada' ? true : false;
    }

    public function items()
    {
        return $this->hasMany(EvaluationContractItem::class, 'evaluation_id');
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