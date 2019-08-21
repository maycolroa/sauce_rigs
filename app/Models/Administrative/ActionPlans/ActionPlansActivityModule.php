<?php

namespace App\Models\Administrative\ActionPlans;

use Illuminate\Database\Eloquent\Model;

class ActionPlansActivityModule extends Model
{
    protected $table = 'sau_action_plans_activity_module';

    public $timestamps = false;

    protected $fillable = [
        'module_id',
        'activity_id',
        'item_id',
        'item_table_name'
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\General\Module', 'module_id');
    }

    public function activity()
    {
        return $this->belongsTo(ActionPlansActivity::class,'activity_id');
    }
}
