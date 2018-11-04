<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Company extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_companies';

    public function users(){
        return $this->belongsToMany('App\User','sau_company_user');
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }
}
