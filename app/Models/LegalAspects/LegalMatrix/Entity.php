<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use Session;

class Entity extends Model
{
    protected $table = 'sau_lm_entities';

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
        return $this->hasMany(Law::class, 'entity_id');
    }

    public function scopeCompany($query)
    {
        return $query->where('sau_lm_entities.company_id', Session::get('company_id'));
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('sau_lm_entities.company_id');
    }

    public function scopeAlls($query)
    {
        return $query->where('sau_lm_entities.company_id', Session::get('company_id'))
            ->orWhereNull('sau_lm_entities.company_id');
    }

    public function scopeInEntity($query, $entity, $typeSearch = 'IN')
    {
        if (COUNT($entity) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_lm_entities.id', $entity);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_lm_entities.id', $entity);
        }

        return $query;
    }
}