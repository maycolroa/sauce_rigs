<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    protected $table = 'sau_lm_laws';

    protected $fillable = [
        'name',
        'law_number',
        'apply_system',
        'law_year',
        'law_type_id',
        'description',
        'observations',
        'risk_aspect_id',
        'entity_id',
        'sst_risk_id',
        'repealed',
        'file'
    ];

    public function lawType()
    {
        return $this->belongsTo(LawType::class);
    }

    public function riskAspect()
    {
        return $this->belongsTo(RiskAspect::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function sstRisk()
    {
        return $this->belongsTo(SstRisk::class);
    }
}
