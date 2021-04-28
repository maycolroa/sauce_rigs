<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class SubProcess extends Model
{
    protected $table = 'sau_rm_sub_processes';

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

    /*public function dangerMatrices()
    {
        return $this->hasMany('App\Models\IndustrialSecure\DangerMatrix\DangerMatrixActivity', 'activity_id');
    }*/
}
