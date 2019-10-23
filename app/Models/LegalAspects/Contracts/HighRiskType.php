<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class HighRiskType extends Model
{
    protected $table = 'sau_ct_high_risk_types';

    protected $fillable = [
        'name'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
