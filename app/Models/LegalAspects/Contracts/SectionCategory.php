<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class SectionCategory extends Model
{
    protected $table = 'sau_ct_section_category';

    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'category_name'
    ];

    public function items()
    {
        return $this->hasMany(SectionCategoryItems::class, 'category_id');
    }
}