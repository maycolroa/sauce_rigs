<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsSocialSecurityPaymentOperator extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_tag_social_security_payment_operator';

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
