<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppExit extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_exits';

    protected $fillable = [
        'company_id',
        'user_id',
        'location_id'
    ];

    public function detail()
    {
        return $this->hasMany('App\Models\IndustrialSecure\Epp\EppExitDetail', 'exit_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
