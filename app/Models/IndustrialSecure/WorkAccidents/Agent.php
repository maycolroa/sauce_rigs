<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Agent extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_aw_agents';

    protected $fillable = [
        'name',
        'code'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
