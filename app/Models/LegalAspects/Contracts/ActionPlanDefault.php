<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ActionPlanDefault extends Model
{
    protected $table = 'sau_ct_action_plan_default';

    public $timestamps = false;

    public function itemsContract(){
        return $this->belongsToMany(SectionCategoryItems::class, 'sau_ct_section_category_items');
    }
}
