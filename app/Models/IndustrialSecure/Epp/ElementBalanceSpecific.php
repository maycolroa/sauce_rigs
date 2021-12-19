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

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\ElementBalanceLocation', 'element_balance_id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\ElementTransactionEmployee', 'sau_epp_transaction_employee_element', 'element_id', 'transaction_employee_id');
    }


}
