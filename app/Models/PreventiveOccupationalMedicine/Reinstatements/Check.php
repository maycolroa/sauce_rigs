<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use App\Traits\CompanyTrait;
use App\Scopes\HeadquartersScope;
use Illuminate\Database\Eloquent\Model;
use Exception;

class Check extends Model
{
    use CompanyTrait;
      /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new HeadquartersScope);
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'employee_id',
        'state',
        'disease_origin',
        'has_recommendations',
        'start_recommendations',
        'end_recommendations',
        'indefinite_recommendations',
        'origin_recommendations',
        'relocated',
        'laterality',
        'detail',
        'monitoring_recommendations',
        'in_process_origin',
        'process_origin_done',
        'process_origin_done_date',
        'emitter_origin',
        'in_process_pcl',
        'process_pcl_done',
        'process_pcl_done_date',
        'pcl',
        'entity_rating_pcl',
        'process_origin_file',
        'process_origin_file_name',
        'process_pcl_file',
        'process_pcl_file_name',
        'cie10_code_id',
        'restriction_id',
        'has_restrictions',
        'relocated_regional_id',
        'relocated_headquarter_id',
        'relocated_process_id',
        'relocated_position_id',
        'date_controversy_origin_1',
        'date_controversy_origin_2',
        'date_controversy_pcl_1',
        'date_controversy_pcl_2',
        'emitter_controversy_origin_1',
        'emitter_controversy_origin_2',
        'emitter_controversy_pcl_1',
        'emitter_controversy_pcl_2',
        'malady_origin',
        'eps_favorability_concept',
        'case_classification',
        'relocated_date',
        'start_restrictions',
        'end_restrictions',
        'indefinite_restrictions',
        'has_incapacitated',
        'incapacitated_days',
        'incapacitated_last_extension',
        'deadline',
        'next_date_tracking',
        'sve_associated',
        'medical_certificate_ueac',
        'relocated_type',
    ];

    /**
     * each check has a only employee
     * @return App\Models\Administrative\Employees\Employee
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'employee_id');
    }

    public function relocatedRegional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'relocated_regional_id');
    }

    public function relocatedHeadquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'relocated_headquarter_id');
    }

    public function relocatedProcess()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'relocated_process_id');
    }

    public function relocatedPosition()
    {
        return $this->belongsTo('App\Models\Administrative\Positions\EmployeePosition', 'relocated_position_id');
    }

    /**
     * returns the cie 10 code model related to this check
     * @return App\Models\PreventiveOccupationalMedicine\Reinstatements\Cie10Code
     */
    public function cie10Code()
    {
        return $this->belongsTo(Cie10Code::class, 'cie10_code_id');
    }

    /**
     * returns the regional model related to this check
     * @return App\Models\Administrative\Regionals\Regional
     */
    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\Regional', 'sau_employees.employee_regional_id');
    }

    /**
     * returns the restriction model related to this check
     * @return App\Models\PreventiveOccupationalMedicine\Reinstatements\Restriction
     */
    public function restriction()
    {
        return $this->belongsTo(Restriction::class, 'restriction_id');
    }

    /**
     * each check has multiple medical monitoring
     * @return App\Models\PreventiveOccupationalMedicine\Reinstatements\MedicalMonitoring
     */
    public function medicalMonitorings()
    {
        return $this->hasMany(MedicalMonitoring::class, 'check_id');
    }

    /**
     * each check has multiple medical monitoring
     * @return App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborMonitoring
     */
    public function laborMonitorings()
    {
        return $this->hasMany(LaborMonitoring::class, 'check_id');
    }

    /**
     * each check has multiple tracings
     * @return App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing
     */
    public function tracings()
    {
        return $this->hasMany(Tracing::class, 'check_id');
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isOpen()
    {
        return $this->state == 'ABIERTO';
    }

    /**
     * filters by the state of the check
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  string $state
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * filters only open/closed check
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Boleam $isOpen
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsOpen($query, $isOpen = true)
    {
        $state = $isOpen ? 'ABIERTO' : 'CERRADO'; 
        return $query->byState($state);
    }

    /**
     * adds the filter for the text type with like condition
     * to the relation and column
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $relation
     * @param string $text
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeAddFilterText($query, $relation, $column, $text)
    {
        return $query->orWhereHas($relation, function ($query) use ($column, $text) {
            $query->where($column, 'like', "%$text%");
        });
    }*/

    /**
     * adds a select raw to the query to count checks
     * per employee_id
     * 
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeCountDistinctEmployeeId($query)
    {
        return $query->selectRaw("COUNT(DISTINCT employee_id) AS count");
    }

    /**
     * this scope is used to make a "secure count" to avoid errors
     * According to the query, fetchs the first register
     * if it does not exist, returns 0
     *
     * if this register exists but it does not have the count attribute, returns 0
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeSecureCount($query)
    {
        $check = $query->first();
        if (!$check) {
            return 0;
        }
        return isset($check->count) ? $check->count : 0;
    }

    /**
     * filters checks through the given regionals
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $regionals
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRegionals($query, $regionals, $typeSearch = 'IN')
    {
        if (COUNT($regionals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_regional_id', $regionals);
        }

        return $query;
    }

    public function scopeInDiseaseOrigin($query, $diseaseOrigin, $typeSearch = 'IN')
    {
        if (COUNT($diseaseOrigin) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.disease_origin', $diseaseOrigin);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.disease_origin', $diseaseOrigin);
        }

        return $query;
    }


    public function scopeInYears($query, $years, $typeSearch = 'IN')
    {
        if (COUNT($years) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereRaw("year(sau_reinc_checks.created_at) IN (".$years->implode(',').")");

            else if ($typeSearch == 'NOT IN')
                $query->whereRaw("year(sau_reinc_checks.created_at) NOT IN (".$years->implode(',').")");
        }
        
        return $query;
    }

    /**
     * filters checks through one specified company
     * according to the company of the specified $user
     *
     * only if the user cannot manage companies
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  App\User $user
     * @return Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeBelongsToCompany($query, User $user)
    {
        if ($user->can('manage_companies')) {
            return $query;
        }
        return $query->where('reinc_checks.sec_company_id', $user->sec_company_id);
    }*/

    /*public function setCreatedAtAttribute($value)
    {
        if(isset($value))
        {
            $this->attributes['created_at'] = $value;
        }
    }*/

    public function scopeInNextFollowDays($query, $nextFollowDays, $typeSearch = 'IN')
    {
        if (COUNT($nextFollowDays) > 0)
        {
            if ($typeSearch == 'IN')
            {
                $query->where(function ($subquery) use ($nextFollowDays) {
                    foreach ($nextFollowDays as $nextFollowDay) {
                        if ($nextFollowDay == 'Vencidas')
                            $subquery->orWhere('sau_reinc_checks.next_date_tracking', '<', date('Y-m-d'));
                        else if ($nextFollowDay == 'No Aplica')
                            $subquery->orWhereNull('sau_reinc_checks.next_date_tracking');
                        else 
                            $subquery->orWhere('sau_reinc_checks.next_date_tracking', $nextFollowDay);
                    }
                });
            }
            else if ($typeSearch == 'NOT IN')
            {
                $query->where(function ($subquery) use ($nextFollowDays) {
                    $vencidas = false;
                    $no_aplica = false;
                    $days = [];

                    foreach ($nextFollowDays as $nextFollowDay) {
                        if ($nextFollowDay == 'Vencidas')
                            $vencidas = true;
                        else if ($nextFollowDay == 'No Aplica')
                            $no_aplica = true;
                        else 
                            array_push($days, $nextFollowDay);
                    }

                    if ($vencidas)
                        $subquery->where('sau_reinc_checks.next_date_tracking', '>=', date('Y-m-d'));

                    if (COUNT($days))
                        $subquery->whereNotIn('sau_reinc_checks.next_date_tracking', $days);

                    if ($no_aplica)
                        $subquery->whereNotNull('sau_reinc_checks.next_date_tracking');
                    else
                        $subquery->orWhereNull('sau_reinc_checks.next_date_tracking');
                });
            }
        }

        return $query;
    }

    /**
     * filters checks through the given businesses
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $businesses
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInBusinesses($query, $businesses, $typeSearch = 'IN')
    {
        if (COUNT($businesses) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_business_id', $businesses);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_business_id', $businesses);
        }

        return $query;
    }

    /**
     * filters checks through the given names
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $names
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInNames($query, $names, $typeSearch = 'IN')
    {
        if (COUNT($names) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.name', $names);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.name', $names);
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_reinc_checks.created_at', $dates);
            return $query;
        }
    }
}
