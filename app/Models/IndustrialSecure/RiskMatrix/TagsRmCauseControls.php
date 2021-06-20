<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsRmCauseControls extends Model
{
    use CompanyTrait;

    protected $table = 'sau_rm_tags_cause_controls';

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
