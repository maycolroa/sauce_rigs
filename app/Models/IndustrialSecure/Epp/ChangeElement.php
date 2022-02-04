<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class ChangeElement extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_transaction_returns_change_elements';

    protected $fillable = [
        'transaction_employee_id',
        'element_id',
        'element_specific_old_id',
        'element_specific_new_id',
        'company_id',
        'user_id',
        'reason'
    ];

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\Element', 'element_id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\ElementTransactionEmployee', 'transaction_employee_id');
    }

    public function specificsNew()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'element_specific_new_id');
    }

    public function specificsOld()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'element_specific_old_id');
    }

}
