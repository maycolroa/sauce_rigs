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
        'responsible_id',
        'user_id',
        'execution_date',
        'expiration_date',
        'state',
        'editable'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_company_user';

    public function responsible()
    {
        return $this->belongsTo('App\User','responsible_id');
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
