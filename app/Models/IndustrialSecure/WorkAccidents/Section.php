<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sau_aw_causes_sections';

    public $timestamps = false;

    protected $fillable = [
        'section_name'
    ];

    public function categories()
    {
        return $this->hasMany(SectionCategory::class, 'section_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->section_name,
            'value' => $this->id
        ];
    }
}