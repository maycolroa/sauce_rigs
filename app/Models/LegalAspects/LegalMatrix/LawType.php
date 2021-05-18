<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use Session;

class LawType extends Model
{
    protected $table = 'sau_lm_laws_types';

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
        return $this->hasMany(Law::class, 'law_type_id');
    }

    public function scopeCompany($query)
    {
        return $query->where('sau_lm_laws_types.company_id', Session::get('company_id'));
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('sau_lm_laws_types.company_id');
    }

    public function scopeAlls($query)
    {
        return $query->where('sau_lm_laws_types.company_id', Session::get('company_id'))
            ->orWhereNull('sau_lm_laws_types.company_id');
    }
}