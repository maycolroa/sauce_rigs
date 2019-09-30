<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Inspection extends Model
{
    use CompanyTrait;
    
    public $table = 'sau_ph_inspections';

    protected $fillable = [
        'name',
        'company_id',
        'state',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function themes()
    {
        return $this->hasMany(InspectionSection::class, 'inspection_id');
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->state == 'SI';
    }

    public function regionals()
    {
        return $this->belongsToMany('App\Models\Administrative\Regionals\EmployeeRegional', 'sau_ph_inspection_regional');
    }

    public function headquarters()
    {
        return $this->belongsToMany('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'sau_ph_inspection_headquarter');
    }

    public function processes()
    {
        return $this->belongsToMany('App\Models\Administrative\Processes\EmployeeProcess', 'sau_ph_inspection_process');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Models\Administrative\Areas\EmployeeArea', 'sau_ph_inspection_area');
    }

    /**
     * filters checks through the given headquarters
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $headquarters
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInHeadquarters($query, $headquarters, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($headquarters as $key => $value)
        {
            $ids[] = $value;
        }

        if (COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_headquarter.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_headquarter.employee_headquarter_id', $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given areas
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $areas
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInAreas($query, $areas, $typeSearch = 'IN')
    {
        if (COUNT($areas) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_area.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_area.employee_area_id', $areas);
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
            $query->whereBetween('sau_ph_inspections.created_at', $dates);
            return $query;
        }
    }
}