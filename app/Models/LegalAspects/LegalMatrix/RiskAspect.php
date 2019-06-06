<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class RiskAspect extends Model
{
    protected $table = 'sau_lm_risks_aspects';

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
}