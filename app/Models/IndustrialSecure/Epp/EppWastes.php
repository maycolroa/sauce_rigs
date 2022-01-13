<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppWastes extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_wastes';

    protected $fillable = [
        'company_id',
        'employee_id',
        'element_id',
        'location_id',
        'code_element'
    ];

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'element_id');
    }
}
