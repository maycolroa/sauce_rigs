<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryItems extends Model
{
    protected $table = 'sau_aw_causes_section_category_items';

    protected $fillable = [
        'category_id',
        'item_name'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->item_name,
            'value' => $this->id
        ];
    }
}
