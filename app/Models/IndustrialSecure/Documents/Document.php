<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;


class ElementBalanceInicialLog extends Model
{
    protected $table = 'sau_epp_elements_balance_inicial_log';

    protected $fillable = [
        'element_id',
        'balance_inicial'
    ];
}
