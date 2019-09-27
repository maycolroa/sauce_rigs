<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class InspectionSection extends Model
{
    public $table = 'sau_ph_inspections_sections';

    protected $fillable = [
        'name',
        'inspection_id'
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function items()
    {
        return $this->hasMany(InspectionSectionItem::class, 'inspection_section_id');
    }
}
