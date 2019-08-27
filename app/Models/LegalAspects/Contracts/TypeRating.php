<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TypeRating extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_types_ratings';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'sau_ct_item_type_rating');
    }
}