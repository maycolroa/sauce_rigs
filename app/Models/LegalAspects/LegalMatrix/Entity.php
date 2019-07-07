<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $table = 'sau_lm_entities';

    protected $fillable = [
        'name',
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
}