<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Company extends Model
{
    //use CompanyTrait;
    
    protected $table = 'sau_companies';

    protected $fillable = [
        'name', 
        'active', 
        'logo', 
        'ph_state_incentives', 
        'ph_file_incentives', 
        'company_group_id', 
        'test',
        'nombre_actividad_economica_sede_principal',
        'tipo_identificacion_sede_principal',
        'identificacion_sede_principal',
        'direccion_sede_principal',
        'telefono_sede_principal',
        'email_sede_principal',
        'departamento_sede_principal_id',
        'ciudad_sede_principal_id',
        'zona_sede_principal'
    ];

    protected $casts = [
        'ph_state_incentives' => 'boolean',
    ];

    public function departament()
    {
        return $this->belongsTo(Departament::class, 'departamento_sede_principal_id');
    }

    public function city()
    {
        return $this->belongsTo(Municipality::class, 'ciudad_sede_principal_id');
    }

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

    public function centers()
    {
        return $this->hasMany(WorkCenter::class, 'company_id');
    }

    public function interests()
    {
        return $this->belongsToMany('App\Models\LegalAspects\LegalMatrix\Interest', 'sau_lm_company_interest');
    }

    public function qualificationMasive()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\DangerousConditions\Inspections\Qualifications', 'sau_ph_qualification_masive_company', 'company_id', 'qualification_id');
    }

    public function qualificationMasiveRs()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\DangerousConditions\Inspections\Qualifications', 'sau_rs_qualification_masive_company', 'company_id', 'qualification_id');
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
