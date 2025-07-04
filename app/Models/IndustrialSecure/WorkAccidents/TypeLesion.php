<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TypeLesion extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_aw_types_lesion';

    protected $fillable = [
        'name',
        'code'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function accident()
    {
        return $this->belongsToMany(Accident::class, 'sau_aw_form_accidents_types_lesion', 'form_accident_id', 'type_lesion_id');
    }
}
