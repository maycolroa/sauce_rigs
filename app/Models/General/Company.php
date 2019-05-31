<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Company extends Model
{
    //use CompanyTrait;
    
    protected $table = 'sau_companies';

    protected $fillable = ['name', 'active'];

    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User','sau_company_user');
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function interests()
    {
        return $this->belongsToMany('App\Models\LegalAspects\LegalMatrix\Interest', 'sau_lm_company_interest');
    }
}
