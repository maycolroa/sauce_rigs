<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppExitDetail extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_exits_details';

    protected $fillable = [
        'exit_id',
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

    public function exit()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\EppExit', 'element_id');
    }

    public function specifics()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'sau_epp_exits_details_elements', 'exit_detail_id', 'element_specific_id');
    }

}
