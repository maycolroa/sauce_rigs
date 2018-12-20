<?php

namespace App\IndustrialSecure;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Activity extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dm_activities';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
