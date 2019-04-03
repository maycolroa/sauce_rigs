<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class ActionPlanDefault extends Model
{
    protected $table = 'sau_ct_action_plan_default';

    public function itemsContract(){
        return $this->belongsToMany('App\Models\LegalAspects\SectionCategoryItems', 'sau_ct_section_category_items');
    }
}
