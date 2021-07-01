<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class SubProcessRisk extends Model
{
    protected $table = 'sau_rm_subprocess_risk';

    public $timestamps = false;

    protected $fillable = [
        'rm_subprocess_id',
        'risk_id',
        'risk_sequence',
        'economic',
        'quality_care_patient_safety',
        'reputational',
        'legal_regulatory',
        'environmental',
        'max_inherent_impact',
        'description_inherent_impact',
        'max_inherent_frequency',
        'description_inherent_frequency',
        'inherent_exposition',
        'controls_decrease',
        'nature',
        'evidence',
        'coverage',
        'documentation',
        'segregation',
        'control_evaluation',
        'percentege_mitigation',
        'max_residual_impact',
        'description_residual_impact',
        'max_residual_frequency',
        'description_residual_frequency',
        'residual_exposition',
        'max_impact_event_risk'
    ];

    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    public function causes()
    {
        return $this->hasMany(Cause::class, 'rm_subprocess_risk_id');
    }

    public function indicators()
    {
        return $this->hasMany(Indicators::class, 'rm_subprocess_risk_id');
    }

    /**
     * filters checks through the given dangers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dangers
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeInDangers($query, $dangers, $typeSearch = 'IN')
    {
        if (COUNT($dangers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dm_activity_danger.danger_id', $dangers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_activity_danger.danger_id', $dangers);
        }

        return $query;
    }*/
}
