<?php

namespace App\LegalAspects;

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
        return $this->belongsToMany(TypeRating::class, 'sau_ct_item_type_rating')->withPivot('apply', 'value');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'item_id');
    }
}