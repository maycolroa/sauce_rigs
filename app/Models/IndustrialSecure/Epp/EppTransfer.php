<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;


class EppTransfer extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_transfers';

    protected $fillable = [
        'company_id',
        'user_id',
        'location_origin_id',
        'location_destiny_id',
        'quantity',
        'state'
    ];

    public function detail()
    {
        return $this->hasMany('App\Models\IndustrialSecure\Epp\EppTransferDetail', 'transfer_id');
    }

    public function origin()
    {
        return $this->belongsTo(Location::class, 'location_origin_id');
    }

    public function destiny()
    {
        return $this->belongsTo(Location::class, 'location_destiny_id');
    }
}
