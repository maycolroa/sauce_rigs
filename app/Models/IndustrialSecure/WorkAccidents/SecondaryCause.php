<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class SecondaryCause extends Model
{
    protected $table = 'sau_aw_accidents_secondary_causes';

    protected $fillable = [
        'main_cause_id',
        'description',
        'section_id'
    ];

    public function tertiary()
    {
        return $this->hasMany(TertiaryCause::class, 'secondary_cause_id');
    }

    public function main()
    {
        return $this->belongsTo(MainCause::class, 'main_cause_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}