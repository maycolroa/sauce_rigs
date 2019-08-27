<?php

namespace App\Models\IndustrialSecure\Dangers;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Danger extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dm_dangers';

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

    public function dangerMatrices()
    {
        return $this->hasMany('App\Models\IndustrialSecure\DangerMatrix\ActivityDanger', 'danger_id');
    }
}
