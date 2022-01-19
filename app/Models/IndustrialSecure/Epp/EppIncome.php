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
        'user_id',
        'location_id'
    ];

    public function detail()
    {
        return $this->hasMany('App\Models\IndustrialSecure\Epp\EppIncomeDetail', 'income_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
