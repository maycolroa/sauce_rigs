<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Person extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_aw_form_accidents_people';

    protected $fillable = [
        'name',
        'position',
        'type_document',
        'document',
        'form_accident_id',
        'rol'
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
        return $this->belongsTo(Accident::class, 'form_accident_id');
    }
}
