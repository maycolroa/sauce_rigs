<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;


class ElementBalanceSpecific extends Model
{
    protected $table = 'sau_epp_elements_balance_specific';

    protected $fillable = [
        'hash',
        'element_balance_id',
        'location_id',
        'code',
        'state',
        'expiration_date'
    ];

}
