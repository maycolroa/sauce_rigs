<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppTransferDetail extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_transfer_details';

    protected $fillable = [
        'transfer_id',
        'company_id',
        'element_id',
        'location_origin_id',
        'location_destiny_id',
        'quantity'
    ];

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\Element', 'element_id');
    }

    public function transfer()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\EppTransfer', 'transfer_id');
    }

    public function specifics()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'sau_epp_transfer_details_element', 'transfer_detail_id', 'element_specific_id');
    }

}
