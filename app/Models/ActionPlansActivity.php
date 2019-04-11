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
        'editable',
        'company_id'
    ];

    //the attribute define the table for scope company execute
    //public $scope_table_for_company_table = 'sau_company_user';
    
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

    /**
     * filters checks through the given responsibles
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $responsibles
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInResponsibles($query, $responsibles, $typeSearch = 'IN')
    {
        if (COUNT($responsibles) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_action_plans_activities.responsible_id', $responsibles);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_action_plans_activities.responsible_id', $responsibles);
        }

        return $query;
    }

    /**
     * filters checks through the given states
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $states
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInStates($query, $states, $typeSearch = 'IN')
    {
        if (COUNT($states) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_action_plans_activities.state', $states);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_action_plans_activities.state', $states);
        }

        return $query;
    }

    /**
     * filters checks through the given modules
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $modules
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInModules($query, $modules, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($modules as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_modules.id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_modules.id', $ids);
        }

        return $query;
    }
}
