<?php

namespace App\Administrative\Configurations;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class LocationLevelForm extends Model
{
    use CompanyTrait;

    protected $table = 'sau_conf_location_level_forms';

    protected $fillable = [
      'company_id',
      'module_id',
      'regional',
      'headquarter',
      'area',
      'process'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'module_id');
    }
}
