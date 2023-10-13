<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryItemsCompany extends Model
{
    protected $table = 'sau_aw_causes_section_category_items_company';

    protected $fillable = [
        'category_id',
        'item_name',
        'company_id'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->item_name,
            'value' => $this->id
        ];
    }
}
