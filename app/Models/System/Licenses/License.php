<?php

namespace App\Models\System\Licenses;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class License extends Model
{
    use CompanyTrait;

    protected $table = 'sau_licenses';

    protected $fillable = [
        'started_at',
        'ended_at',
        'company_id',
        'notified',
        'user_id',
        'freeze',
        'available_days',
        'reassigned',
        'start_freeze',
        'date_freeze',
        'observations'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\General\Company');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User');
    }

    public function modules()
    {
        return $this->belongsToMany('App\Models\General\Module','sau_license_module');
    }

    public function modulesFreeze()
    {
        return $this->belongsToMany('App\Models\General\Module','sau_license_module_freeze');
    }

    public function scopeSystem($query)
    {
      return $query->withoutGlobalScopes();
    }

    public function histories()
    {
        return $this->hasMany(LicenseHistory::class, 'license_id');
    }

    public function scopeNotNotified($query)
    {
        return $query->where('notified', 'NO');
    }

    public function scopeInModules($query, $modules, $typeSearch = 'IN')
    {
        if (COUNT($modules) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_license_module.module_id', $modules);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_license_module.module_id', $modules);
        }

        return $query;
    }

    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_licenses.started_at', $dates);
            return $query;
        }
    }

    public function scopeInGroups($query, $modules, $typeSearch = 'IN')
    {
        if (COUNT($modules) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_companies.company_group_id', $modules);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_companies.company_group_id', $modules);
        }

        return $query;
    }

    public function scopeInFreeze($query, $options, $typeSearch = 'IN')
    {
        if (COUNT($options) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_licenses.freeze', $options);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_licenses.freeze', $options);
        }

        return $query;
    }
}
