<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class SstRisk extends Model
{
    protected $table = 'sau_lm_sst_risks';

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
        return $this->hasMany(Law::class, 'sst_risk_id');
    }
}