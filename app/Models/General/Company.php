<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Company extends Model
{
    //use CompanyTrait;
    
    protected $table = 'sau_companies';

    protected $fillable = ['name', 'active', 'logo', 'ph_state_incentives', 'ph_file_incentives'];

    protected $casts = [
        'ph_state_incentives' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\Administrative\Users\User','sau_company_user');
    }

    public function licenses()
    {
        return $this->hasMany('App\Models\System\Licenses\License');
    }

    public function interests()
    {
        return $this->belongsToMany('App\Models\LegalAspects\LegalMatrix\Interest', 'sau_lm_company_interest');
    }

    public function itemStandardCompany()
    {
        return $this->belongsToMany('App\Models\LegalAspects\Contracts\SectionCategoryItems', 'sau_ct_standard_items_required')->withPivot('required');
    }

    public function multiselect()
    {
        return [
          'name' => $this->name,
          'value' => $this->id
        ];
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->active == 'SI';
    }
}
