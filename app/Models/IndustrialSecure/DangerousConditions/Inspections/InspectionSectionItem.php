<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;

class InspectionSectionItem extends Model
{
    public $table = 'sau_ph_inspection_section_items';

    protected $fillable = [
        'description',
        'inspection_section_id',
        'compliance_value',
        'partial_value'
    ];

    public function section()
    {
        return $this->belongsTo(InspectionSection::class, 'inspection_section_id');
    }

    public function qualifications()
    {
        return $this->hasMany(InspectionItemsQualificationAreaLocation::class, 'item_id');
    }
}
