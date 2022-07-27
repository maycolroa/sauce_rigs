<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class MainCause extends Model
{
    protected $table = 'sau_aw_accidents_main_causes';

    protected $fillable = [
        'accident_id',
        'description'
    ];

    public function secondary()
    {
        return $this->hasMany(SecondaryCause::class, 'main_cause_id');
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }
}