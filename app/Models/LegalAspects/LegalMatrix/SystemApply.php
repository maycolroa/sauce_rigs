<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use Session;

class SystemApply extends Model
{
    protected $table = 'sau_lm_system_apply';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function laws()
    {
        return $this->hasMany(Law::class, 'system_apply_id');
    }

    public function scopeCompany($query)
    {
        return $query->where('sau_lm_system_apply.company_id', Session::get('company_id'));
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('sau_lm_system_apply.company_id');
    }

    public function scopeAlls($query)
    {
        return $query->where('sau_lm_system_apply.company_id', Session::get('company_id'))
                     ->orWhereNull('sau_lm_system_apply.company_id');
    }

    public function scopeInSystemApply($query, $systemApply, $typeSearch = 'IN')
    {
        if (COUNT($systemApply) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_system_apply.id', $systemApply);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_system_apply.id', $systemApply);
        }

        return $query;
    }
}