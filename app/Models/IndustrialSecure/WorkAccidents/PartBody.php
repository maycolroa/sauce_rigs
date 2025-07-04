<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class PartBody extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_aw_parts_body';

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
        return $this->belongsToMany(Accident::class, 'sau_aw_form_accidents_parts_body', 'form_accident_id', 'part_body_id');
    }
}
