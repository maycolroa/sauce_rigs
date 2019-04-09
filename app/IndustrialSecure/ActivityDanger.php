<?php

namespace App\IndustrialSecure;

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
    ];

    public function danger()
    {
        return $this->belongsTo(Danger::class, 'danger_id');
    }

    public function qualifications()
    {
        return $this->hasMany(QualificationDanger::class, 'activity_danger_id');
    }

}
