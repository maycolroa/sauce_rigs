<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class Indicators extends Model
{
    protected $table = 'sau_rm_risk_indicators';

    protected $fillable = [        
        'rm_subprocess_risk_id',
        'indicator'
    ];

    public $timestamps = false;

    public function subProcessRisk()
    {
        return $this->belongsTo(SubProcessRisk::class, 'rm_subprocess_risk_id');
    }

    /*public function dangers()
    {
        return $this->hasMany(ActivityDanger::class, 'dm_activity_id');
    }*/
}
