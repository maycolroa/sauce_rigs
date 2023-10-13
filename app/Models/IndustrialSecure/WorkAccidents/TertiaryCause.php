<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;

class TertiaryCause extends Model
{
    protected $table = 'sau_aw_accidents_tertiary_causes';

    protected $fillable = [
        'secondary_cause_id',
        'description',
        'category_id',
        'item_id'
    ];

    public function secondary()
    {
        return $this->belongsTo(SecondaryCause::class, 'secondary_cause_id');
    }

    public function category()
    {
        return $this->belongsTo(SectionCategoryCompany::class, 'category_id');
    }

    public function item()
    {
        return $this->belongsTo(SectionCategoryItemsCompany::class, 'item_id');
    }
}