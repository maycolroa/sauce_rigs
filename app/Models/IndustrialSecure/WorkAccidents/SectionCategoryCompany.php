<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryCompany extends Model
{
    protected $table = 'sau_aw_causes_section_category_company';

    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'category_name',
        'company_id',
        'category_default_id'
    ];

    public function items()
    {
        return $this->hasMany(SectionCategoryItems::class, 'category_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->category_name,
            'value' => $this->id
        ];
    }
}