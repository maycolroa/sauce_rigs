<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class SectionCategoryItems extends Model
{
    protected $table = 'sau_ct_section_category_items';

    public function users(){
        return $this->belongsToMany('App\User', 'sau_users');
    }
}
