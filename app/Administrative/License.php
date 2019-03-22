<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class License extends Model
{
    use CompanyTrait;

    protected $table = 'sau_licenses';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function modules()
    {
        return $this->belongsToMany('App\Models\Module','sau_license_module');
    }
}
