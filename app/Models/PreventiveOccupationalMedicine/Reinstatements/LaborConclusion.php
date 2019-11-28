<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class LaborConclusion extends Model
{
    use CompanyTrait;

    protected $table = 'sau_reinc_labor_conclusions';

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

    /*public function checks()
    {
        return $this->hasMany(Check::class, 'restriction_id');
    }*/
}
