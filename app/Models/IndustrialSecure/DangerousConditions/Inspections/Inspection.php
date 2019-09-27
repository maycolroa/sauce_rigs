<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Inspection extends Model
{
    use CompanyTrait;
    
    public $table = 'sau_ph_inspections';

    protected $fillable = [
        'name',
        'company_id',
        'state',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function themes()
    {
        return $this->hasMany(InspectionSection::class, 'inspection_id');
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->state == 'SI';
    }

    public function regionals()
    {
        return $this->belongsToMany('App\Models\Administrative\Regionals\EmployeeRegional', 'sau_ph_inspection_regional');
    }

    public function headquarters()
    {
        return $this->belongsToMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'sau_ph_inspection_headquarter');
    }

    public function processes()
    {
        return $this->belongsToMany('App\Models\Administrative\Processes\EmployeeProcess', 'sau_ph_inspection_process');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Models\Administrative\Areas\EmployeeArea', 'sau_ph_inspection_area');
    }
}