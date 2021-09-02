<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class CauseControl extends Model
{
    protected $table = 'sau_rm_cause_controls';

    protected $fillable = [        
        'rm_cause_id',
        'controls',
        'number_control',
        'nomenclature'
    ];

    public $timestamps = false;

    /*public function subProcessRisk()
    {
        return $this->belongsTo(Cause::class, 'rm_cause_id');
    }*/

    /*public function dangers()
    {
        return $this->hasMany(ActivityDanger::class, 'dm_activity_id');
    }*/
}
