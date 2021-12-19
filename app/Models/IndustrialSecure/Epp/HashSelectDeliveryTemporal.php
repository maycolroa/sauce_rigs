<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class HashSelectDeliveryTemporal extends Model
{

    protected $table = 'sau_epp_hash_selected_delivery_temporal';

    protected $fillable = [
        'element_id',
        'location_id',
        'hash'
    ];
}
