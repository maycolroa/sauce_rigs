<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Vehicle extends Model
{
    use CompanyTrait;

    protected $table = 'sau_rs_vehicles';
    
    protected $fillable = [
        'company_id',
        'plate',
        'name_propietary',
        'registration_number',
        'registration_number_date',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_area_id',
        'employee_process_id',
        'type_vehicle',
        'code_vehicle',
        'mark',
        'line',
        'model',
        'cylinder_capacity',
        'color',
        'chassis_number',
        'engine_number',
        'passenger_capacity',
        'loading_capacity',
        'state',
        'soat_number',
        'insurance',
        'expedition_date_soat',
        'due_date_soat',
        'file_soat',
        'mechanical_tech_number',
        'issuing_entity',
        'expedition_date_mechanical_tech',
        'due_date_mechanical_tech',
        'file_mechanical_tech',
        'policy_responsability',
        'policy_number',
        'policy_entity',
        'expedition_date_policy',
        'due_date_policy',
        'file_policy',
    ];

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'employee_headquarter_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'employee_area_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'employee_process_id');
    }

    /*public function documents()
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
    }*/
}