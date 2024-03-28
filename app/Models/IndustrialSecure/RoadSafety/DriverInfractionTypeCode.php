<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class DriverInfractionTypeCode extends Model
{

    protected $table = 'sau_rs_infractions_type_codes';
    
    protected $fillable = [
        'type_id',
        'code',
        'description',
    ];
    
    public function typeInfraction()
    {
        return $this->belongsTo(DriverInfractionType::class, 'type_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->code,
            'value' => $this->id
        ];
    }
}