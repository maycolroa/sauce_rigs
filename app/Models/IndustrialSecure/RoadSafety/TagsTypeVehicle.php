<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsTypeVehicle extends Model
{
    use CompanyTrait;

    protected $table = 'sau_rs_tag_type_vehicles';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
