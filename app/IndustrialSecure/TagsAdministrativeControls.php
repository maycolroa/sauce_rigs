<?php

namespace App\IndustrialSecure;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsAdministrativeControls extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_tags_administrative_controls';

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
