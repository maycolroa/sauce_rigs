<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Site extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_aw_sites';

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
