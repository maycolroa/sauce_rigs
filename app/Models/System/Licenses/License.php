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
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\General\Company');
    }

    public function modules()
    {
        return $this->belongsToMany('App\Models\General\Module','sau_license_module');
    }

    public function scopeSystem($query)
    {
      return $query->withoutGlobalScopes();
    }

    public function histories()
    {
        return $this->hasMany(LicenseHistory::class, 'license_id');
    }
}
