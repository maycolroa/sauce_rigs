<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppIncomeDetail extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_incomen_details';

    protected $fillable = [
        'income_id',
        'company_id',
        'element_id',
        'location_id',
        'quantity',
        'reason'
    ];

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\Element', 'element_id');
    }

    public function income()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\EppIncome', 'element_id');
    }

    public function specifics()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'sau_epp_income_detail_element', 'income_detail_id', 'element_specific_id');
    }

}
