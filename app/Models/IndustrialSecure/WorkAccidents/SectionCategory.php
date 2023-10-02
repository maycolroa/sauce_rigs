<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class SectionCategory extends Model
{
    protected $table = 'sau_aw_causes_section_category';

    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'category_name'
    ];

    public function items()
    {
        return $this->hasMany(SectionCategoryItems::class, 'category_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->category_name,
            'value' => $this->id
        ];
    }
}