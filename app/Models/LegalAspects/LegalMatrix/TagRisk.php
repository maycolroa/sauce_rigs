<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagRisk extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_tag_risks';

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
