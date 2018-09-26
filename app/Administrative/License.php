<?php

namespace App\Administrative;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $table = 'sau_licenses';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
