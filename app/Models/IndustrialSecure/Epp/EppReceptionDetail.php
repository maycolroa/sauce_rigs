<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppReceptionDetail extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_receptions_details';

    protected $fillable = [
        'reception_id',
        'company_id',
        'element_id',
        'location_origin_id',
        'location_destiny_id',
        'reception',
        'quantity_transfer',
        'quantity_reception',
        'reason'
    ];

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\Element', 'element_id');
    }

    public function reception()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\EppReception', 'reception_id');
    }

    public function specifics()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'sau_epp_receptions_details_elements', 'reception_detail_id', 'element_specific_id');
    }

    public function received()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'sau_epp_receptions_details_elements_received', 'reception_detail_id', 'element_specific_id');
    }

}
