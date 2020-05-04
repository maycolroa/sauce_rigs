<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryItems extends Model
{
    protected $table = 'sau_ct_section_category_items';

    public function activities(){
        return $this->belongsToMany(ActionPlanDefault::class, 'sau_ct_action_items_contract', 'item_id', 'action_plan_id');
    }

    public function itemStandardCompany($company_id)
    {
        return $this->belongsToMany('App\Models\General\Company', 'sau_ct_standard_items_required')->wherePivot('company_id', $company_id)->withPivot('required');
    }

    public function fileSyncInfo(){
        return $this->belongsToMany(FileUploadItemsDetail::class,'sau_ct_file_item_contract', 'item_id', 'file_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->item_name,
            'value' => $this->id
        ];
    }

    public function scopeInStandard($query, $itemStandar, $typeSearch = 'IN')
    {
        if (COUNT($itemStandar) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_section_category_items.id', $itemStandar);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_section_category_items.id', $itemStandar);
        }

        return $query;
    }
}
