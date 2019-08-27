<?php

namespace App\Models\System\Licenses;

use Illuminate\Database\Eloquent\Model;

class LicenseHistory extends Model
{
    protected $table = 'sau_license_histories';

    protected $fillable = [
        'license_id',
        'user_id'
    ];
}
