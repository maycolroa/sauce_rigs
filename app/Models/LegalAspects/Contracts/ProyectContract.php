<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
//use App\Traits\CompanyTrait;

class ProyectContract extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_ct_proyects';
    
    protected $fillable = [
        'company_id',
        'name'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}