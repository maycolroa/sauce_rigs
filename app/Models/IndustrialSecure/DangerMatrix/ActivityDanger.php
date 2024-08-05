<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class ActivityDanger extends Model
{
    protected $table = 'sau_dm_activity_danger';

    public $timestamps = false;

    protected $fillable = [
        'dm_activity_id',
        'danger_id',
        'danger_description',
        'danger_generated',
        'possible_consequences_danger',
        'generating_source',
        'collaborators_quantity',
        'esd_quantity',
        'visitor_quantity',
        'student_quantity',
        'esc_quantity',
        'observations',
        'existing_controls_engineering_controls',
        'existing_controls_substitution',
        'existing_controls_warning_signage',
        'existing_controls_administrative_controls',
        'existing_controls_epp',
        'legal_requirements',
        'quality_policies',
        'objectives_goals',
        'risk_acceptability',
        'intervention_measures_elimination',
        'intervention_measures_substitution',
        'intervention_measures_engineering_controls',
        'intervention_measures_warning_signage',
        'intervention_measures_administrative_controls',
        'intervention_measures_epp',
        'qualification',
        'observation_qualifications'
    ];

    public function danger()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Dangers\Danger', 'danger_id');
    }

    public function qualifications()
    {
        return $this->hasMany(QualificationDanger::class, 'activity_danger_id');
    }

    public function positions()
    {
        return $this->belongsToMany('App\Models\Administrative\Positions\EmployeePosition', 'sau_dm_activity_danger_positions', 'activity_danger_id');
    }

    public function scopeInPositions($query, $positions, $typeSearch = 'IN')
    {
        if (COUNT($positions) > 0)
        {
            if ($typeSearch == 'IN')
                $query->leftJoin('sau_dm_activity_danger_positions', 'sau_dm_activity_danger_positions.activity_danger_id', 'sau_dm_activity_danger.id')->whereIn('sau_dm_activity_danger_positions.employee_position_id', $positions);

            else if ($typeSearch == 'NOT IN')
                $query->leftJoin('sau_dm_activity_danger_positions', 'sau_dm_activity_danger_positions.activity_danger_id', 'sau_dm_activity_danger.id')->whereNotIn('sau_dm_activity_danger_positions.employee_position_id', $positions);
        }
    }

    /**
     * filters checks through the given dangers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dangers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDangers($query, $dangers, $typeSearch = 'IN')
    {
        if (COUNT($dangers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dm_activity_danger.danger_id', $dangers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_activity_danger.danger_id', $dangers);
        }

        return $query;
    }

    /**
     * filters checks through the given macroprocesses
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $macroprocesses
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDangerDescription($query, $dangerdescription, $typeSearch = 'IN')
    {
        $regexp = [];

        foreach ($dangerdescription as $key => $value)
        {
            $regexp[] = "((^|,)($value)(,|$))";
        }

        if (COUNT($regexp) > 0)
        {
            $regexp = implode("|", $regexp);

            if ($typeSearch == 'IN')
                $query->where('sau_dm_activity_danger.danger_description', 'REGEXP', $regexp);

            else if ($typeSearch == 'NOT IN')
                $query->where('sau_dm_activity_danger.danger_description', 'NOT REGEXP', $regexp);
        }

        return $query;
    }

    public function setDangerGeneratedAttribute($value)
    {
      if($value != null)
      {
        $danger_generated = [];

        if (is_array($value)) //Formulario
        {
          foreach($value as $v)
          {
            array_push($danger_generated, $v["value"]);
          }
          
          $this->attributes['danger_generated'] = implode(",", $danger_generated);
        }
        else
        {
            $data = explode(",", $value);
            $item = collect([]);

            foreach ($data as $value)
            {
                $item->push(trim($value));
            }
            
            $this->attributes['danger_generated'] = $item->implode(",");
        }
      }
    }
}
