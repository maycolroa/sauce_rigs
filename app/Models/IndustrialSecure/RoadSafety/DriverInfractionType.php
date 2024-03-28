<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class DriverInfractionType extends Model
{

    protected $table = 'sau_rs_infractions_type';
    
    protected $fillable = [
        'name',
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}