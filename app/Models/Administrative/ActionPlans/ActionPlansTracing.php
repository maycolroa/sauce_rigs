<?php

namespace App\Models\Administrative\ActionPlans;

use Illuminate\Database\Eloquent\Model;

class ActionPlansTracing extends Model
{    
    protected $table = 'sau_action_plan_activities_tracing';

    protected $fillable = [
        'activity_id',
        'user_id',
        'tracing',
    ];

    //the attribute define the table for scope company execute
    //public $scope_table_for_company_table = 'sau_company_user';

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','user_id');
    }

    public function activity()
    {
        return $this->belongsTo(ActionPlansActivity::class, 'activity_id');
    }
}
