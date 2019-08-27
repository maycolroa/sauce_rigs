<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class StandardClassification extends Model
{
    protected $table = 'sau_ct_standard_classification';

    protected $fillable = [
        'standard_name'
    ];

    public function items()
    {
        return $this->belongsToMany(SectionCategoryItems::class, 'sau_ct_items_standard', 'standard_id', 'item_id');
    }
}