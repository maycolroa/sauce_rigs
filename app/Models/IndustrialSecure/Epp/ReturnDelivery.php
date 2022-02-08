<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;

class ReturnDelivery extends Model
{
    protected $table = 'sau_epp_transactions_returns_delivery';

    protected $fillable = [
        'transaction_employee_id',
        'delivery_id',
    ];

    public function delivery()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\ElementTransactionEmployee', 'delivery_id');
    }

}
