<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;

class TagsApplicableStandard extends Model
{

    protected $table = 'sau_epp_tags_applicable_standard';

    protected $fillable = [
        'name',
        'system',
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
