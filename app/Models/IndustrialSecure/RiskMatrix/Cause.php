<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    protected $table = 'sau_rm_causes';

    protected $fillable = [        
        'rm_subprocess_risk_id',
        'cause'
    ];

    public $timestamps = false;

    public function subProcessRisk()
    {
        return $this->belongsTo(SubProcessRisk::class, 'rm_subprocess_risk_id');
    }

    public function controls()
    {
        return $this->hasMany(CauseControl::class, 'rm_cause_id');
    }

    /*public function dangers()
    {
        return $this->hasMany(ActivityDanger::class, 'dm_activity_id');
    }*/
}
