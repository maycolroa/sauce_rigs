<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ActionPlansActivity extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_action_plans_activities';

    protected $fillable = [
        'description',
        'employee_id',
        'user_id',
        'execution_date',
        'expiration_date',
        'state',
        'editable'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees';

    public function employee()
    {
        return $this->belongsTo('App\Administrative\Employee','employee_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function activityModule()
    {
        return $this->hasOne(ActionPlansActivityModule::class, 'activity_id');
    }
}
