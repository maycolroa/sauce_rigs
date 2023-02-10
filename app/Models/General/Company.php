<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Company extends Model
{
    //use CompanyTrait;
    
    protected $table = 'sau_companies';

    protected $fillable = ['name', 'active', 'logo', 'ph_state_incentives', 'ph_file_incentives', 'company_group_id'];

    protected $casts = [
        'ph_state_incentives' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(CompanyGroup::class, 'company_group_id');
    }

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

    public function qualificationMasive()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\DangerousConditions\Inspections\Qualifications', 'sau_ph_qualification_masive_company', 'company_id', 'qualification_id');
    }

    public function itemStandardCompany()
    {
        return $this->belongsToMany('App\Models\LegalAspects\Contracts\SectionCategoryItems', 'sau_ct_standard_items_required')->withPivot('required');
    }

    public function scopeInGroups($query, $groups, $typeSearch = 'IN')
    {
        if (COUNT($groups) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_companies.company_group_id', $groups);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_companies.company_group_id', $groups);
        }

        return $query;
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
