<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionPlansActivity extends Model
{
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
