<?php

namespace App\Models\General;

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
        return $this->belongsToMany(Module::class,'sau_license_module');
    }
}
