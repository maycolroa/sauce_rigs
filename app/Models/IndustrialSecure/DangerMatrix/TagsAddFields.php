<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsAddFields extends Model
{
    use CompanyTrait;

    protected $table = 'sau_tags_dm_add_fields';

    protected $fillable = [
        'name',
        'company_id',
        'field_id'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
