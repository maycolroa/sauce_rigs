<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;


class ElementBalanceLocation extends Model
{
    protected $table = 'sau_epp_elements_balance_ubication';

    protected $fillable = [
        'element_id',
        'location_id',
        'quantity',
        'quantity_available',
        'quantity_allocated',
    ];
}
