<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use App\Traits\CompanyTrait;
use App\Scopes\HeadquartersScope;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
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
        'disease_origin_date',
        'has_recommendations',
        'start_recommendations',
        'end_recommendations',
        'indefinite_recommendations',
        'origin_recommendations',
        'relocated',
        'laterality',
        'qualification_dme',
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
        'cie10_code_2_id',
        'cie10_code_3_id',
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
        'created_at',
        'type_controversy_origin_1',
        'type_controversy_origin_2',
        'punctuation_controversy_plc_1',
        'punctuation_controversy_plc_2',
        'qualification_controversy_1',
        'is_firm_controversy_1',
        'qualification_controversy_2',
        'is_firm_controversy_pcl_1',
        'qualification_origin',
        'is_firm_process_origin',
        'is_firm_process_pcl',
        'type_qualification_origin',
        'subsystem',
        'conclusion_recommendations',
        'position_functions_assigned_reassigned',
        'reinstatement_condition',
        'recommendations_validity',
        'has_function_setting',
        'function_setting',
        'Observations_recommendatios',
        'refund_classification',
        'motive_close',
        'start_incapacitated',
        'end_incapacitated',
        'cie10_code_4_id',
        'cie10_code_5_id',
        'disease_origin_2',
        'disease_origin_3',
        'disease_origin_4',
        'disease_origin_5',
        'laterality_2',
        'laterality_3',
        'laterality_4',
        'laterality_5',
        'qualification_dme_2',
        'qualification_dme_3',
        'qualification_dme_4',
        'qualification_dme_5',
        'reinforced_job_stability',
        'date_new_valoration',
        'disease_origin_recomendations',
        'disease_origin_recomendations_2',
        'disease_origin_recomendations_3',
        'disease_origin_recomendations_4',
        'disease_origin_recomendations_5',
        'use_cie_10',
        'update_cie_11',
        'cie11_code_id'
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

    public function cie11Code()
    {
        return $this->belongsTo(Cie11Code::class, 'cie11_code_id');
    }

    public function cie10Code2()
    {
        return $this->belongsTo(Cie10Code::class, 'cie10_code_2_id');
    }

    public function cie10Code3()
    {
        return $this->belongsTo(Cie10Code::class, 'cie10_code_3_id');
    }

    public function cie10Code4()
    {
        return $this->belongsTo(Cie10Code::class, 'cie10_code_4_id');
    }

    public function cie10Code5()
    {
        return $this->belongsTo(Cie10Code::class, 'cie10_code_5_id');
    }

    public function files()
    {
        return $this->hasMany(CheckFile::class, 'check_id');
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
     * each check has multiple tracings
     * @return App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing
     */
    public function laborNotes()
    {
        return $this->hasMany(LaborNotes::class, 'check_id');
    }

    public function laborNotesRelations()
    {
        return $this->hasMany(LaborNotes::class, 'check_id');
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
        return $query->where('sau_reinc_checks.state', $state);
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

    public function scopeInHeadquarters($query, $headquarters, $typeSearch = 'IN')
    {
        if (COUNT($headquarters) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_headquarter_id', $headquarters);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_headquarter_id', $headquarters);
        }

        return $query;
    }

    public function scopeInProcesses($query, $processes, $typeSearch = 'IN')
    {
        if (COUNT($processes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_process_id', $processes);
        }

        return $query;
    }

    public function scopeInAreas($query, $areas, $typeSearch = 'IN')
    {
        if (COUNT($areas) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.employee_area_id', $areas);
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

    public function getCreatedAtAttribute($timestamp)
    {
        return Carbon::parse($timestamp)->format('D M d Y');
    }

    public function setCreatedAtAttribute($value)
    {
        if (isset($value))
        {
            try
            {
                $this->attributes['created_at'] = (Carbon::createFromFormat('D M d Y', $value))->format('Y-m-d 00:00:00');
            }
            catch (\Exception $e) {
                $this->attributes['created_at'] = $value;
            }
        }
            
    }

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
     * filters checks through the given identifications
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $identifications
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInIdentifications($query, $identifications, $typeSearch = 'IN')
    {
        if (COUNT($identifications) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_employees.identification', $identifications);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_employees.identification', $identifications);
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
            $query->whereBetween('sau_reinc_checks.updated_at', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given sveAssociateds
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $sveAssociateds
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInSveAssociateds($query, $sveAssociateds, $typeSearch = 'IN')
    {
        if (COUNT($sveAssociateds) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.sve_associated', $sveAssociateds);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.sve_associated', $sveAssociateds);
        }

        return $query;
    }

    /**
     * filters checks through the given medicalCertificates
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $medicalCertificates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInMedicalCertificates($query, $medicalCertificates, $typeSearch = 'IN')
    {
        if (COUNT($medicalCertificates) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.medical_certificate_ueac', $medicalCertificates);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.medical_certificate_ueac', $medicalCertificates);
        }

        return $query;
    }

    /**
     * filters checks through the given relocatedTypes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $relocatedTypes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRelocatedTypes($query, $relocatedTypes, $typeSearch = 'IN')
    {
        if (COUNT($relocatedTypes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.relocated_type', $relocatedTypes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.relocated_type', $relocatedTypes);
        }

        return $query;
    }

    public function scopeInCodCie($query, $codsCie, $typeSearch = 'IN')
    {
        if (!is_array($codsCie))
            $codsCie = $codsCie ? $codsCie->toArray() : [];

        if ($codsCie && is_array($codsCie) && COUNT($codsCie) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.cie10_code_id', $codsCie);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.cie10_code_id', $codsCie);
        }

        return $query;
    }

    public function scopeInCodCie11($query, $codsCie, $typeSearch = 'IN')
    {
        if (!is_array($codsCie))
            $codsCie = $codsCie ? $codsCie->toArray() : [];

        if ($codsCie && is_array($codsCie) && COUNT($codsCie) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.cie11_code_id', $codsCie);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.cie11_code_id', $codsCie);
        }

        return $query;
    }

    public function scopeInHaveRecommendations($query, $options, $typeSearch = 'IN')
    {
        if (COUNT($options) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_reinc_checks.has_recommendations', $options);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_reinc_checks.has_recommendations', $options);
        }

        return $query;
    }

    public function scopeInExpiratedRecommendations($query, $options, $typeSearch = 'IN')
    {
        if (COUNT($options) > 0)
        {
            $now = Carbon::now()->format('Y-m-d');

            if ($typeSearch == 'IN')
            {
                if ($options[0] == 'SI')
                    $query->whereRaw("$now < sau_reinc_checks.end_recommendations");
                else if ($options[0] == 'NO')
                    $query->whereRaw("$now > sau_reinc_checks.end_recommendations");
                
            }
            else if ($typeSearch == 'NOT IN')
            {
                if ($options[0] == 'SI')
                    $query->whereRaw("$now > sau_reinc_checks.end_recommendations");
                else if ($options[0] == 'NO')
                    $query->whereRaw("$now < sau_reinc_checks.end_recommendations");
                
            }
        }

        return $query;
    }
}
