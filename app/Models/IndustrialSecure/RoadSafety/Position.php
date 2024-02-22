<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Position extends Model
{
    use CompanyTrait;

    protected $table = 'sau_rs_positions';
    
    protected $fillable = [
        'company_id',
        'name',
        'employee_position_id'
    ];

    public function documents()
    {
        return $this->hasMany(PositionDocument::class, 'position_id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Administrative\Positions\EmployeePosition', 'employee_position_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}