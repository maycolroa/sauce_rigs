<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppIncome extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_incomen';

    protected $fillable = [
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
}
