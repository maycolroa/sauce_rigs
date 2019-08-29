<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Restriction extends Model
{
    use CompanyTrait;

    protected $table = 'sau_reinc_restrictions';

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

    public function checks()
    {
        return $this->hasMany(Check::class, 'restriction_id');
    }
}
