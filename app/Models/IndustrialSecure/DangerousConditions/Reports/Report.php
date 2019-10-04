<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Carbon\Carbon;
use Exception;

class Report extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ph_reports';
    
    protected $fillable = [
        'company_id',
        'observation',
        'image_1',
        'image_2',
        'image_3',
        'rate',
        'condition_id',
        'user_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_process_id',
        'employee_area_id',
        'other_condition'
    ];

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'employee_headquarter_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'employee_process_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'employee_area_id');
    } 

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','user_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class)->where([['id', '<>', '98'],['id', '<>', '114']]);
    }
}
