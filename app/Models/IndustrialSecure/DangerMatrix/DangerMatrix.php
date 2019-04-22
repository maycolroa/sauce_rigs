<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class DangerMatrix extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dangers_matrix';

    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_area_id',
        'employee_process_id',
    ];
     
    public function activities()
    {
        return $this->hasMany(DangerMatrixActivity::class, 'danger_matrix_id');
    }

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

    public function histories()
    {
        return $this->hasMany(ChangeHistory::class, 'danger_matrix_id');
    }

    public function competitors()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User', 'sau_dm_competitors', 'danger_matrix_id');
    }
}
