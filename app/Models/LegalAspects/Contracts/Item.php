<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'sau_ct_items';

    protected $fillable = [
        'subobjective_id',
        'description'
    ];

    public function ratingsTypes()
    {
        return $this->belongsToMany(TypeRating::class, 'sau_ct_item_type_rating')->withPivot('apply');
    }
}