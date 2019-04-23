<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryItems extends Model
{
    protected $table = 'sau_ct_section_category_items';

    public function activities(){
        return $this->belongsToMany(ActionPlanDefault::class, 'sau_ct_action_items_contract', 'item_id', 'action_plan_id');
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
}
