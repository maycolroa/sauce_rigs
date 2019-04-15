<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryItems extends Model
{
    protected $table = 'sau_ct_section_category_items';

    public function activities(){
        return $this->belongsToMany('App\Models\LegalAspects\ActionPlanDefault', 'sau_ct_action_items_contract', 'item_id', 'action_plan_id');
    }

    public function fileSyncInfo(){
        return $this->belongsToMany('App\Models\LegalAspects\FileUploadItemsDetail','sau_ct_file_item_contract', 'item_id', 'file_id');
    }
}
