<?php

namespace App\Models\Administrative\ActionPlans;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;


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
        'company_id',
        'observation',
        'detail_procedence',
        'evidence'
    ];

    //the attribute define the table for scope company execute
    //public $scope_table_for_company_table = 'sau_company_user';
    
    public function responsible()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','responsible_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','user_id');
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
    public function scopeInModules($query, $modules, $typeSearch = 'IN', $riskModule = false, $riskLegal = false, $company_id = NULL)
    {
        $ids = [];

        foreach ($modules as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($riskModule && !$riskLegal)
                $ids = array_diff($ids, array(17));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_modules.id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_modules.id', $ids);

            if ($riskModule)
            {
                $company = $company_id ? $company_id : Session::get('company_id');

                if ($typeSearch == 'IN')
                    $query->orWhereRaw("sau_action_plans_activity_module.item_table_name = 'sau_lm_law_risk_opportunity' and company_id = $company");
                else if ($typeSearch == 'NOT IN')
                    $query->orWhereRaw("sau_action_plans_activity_module.item_table_name <> 'sau_lm_law_risk_opportunity' and company_id = $company");
            }
        }

        return $query;
    }

    public function scopeInUsers($query, $users, $typeSearch = 'IN')
    {
        if (COUNT($users) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_action_plans_activities.user_id', $users);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_action_plans_activities.user_id', $users);
        }

        return $query;
    }

    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_action_plans_activities.expiration_date', $dates);
            return $query;
        }
    }
}
