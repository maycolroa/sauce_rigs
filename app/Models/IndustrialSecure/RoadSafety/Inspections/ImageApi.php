<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class ImageApi extends Model
{
    protected $table = 'sau_rs_images_api';
   
    protected $fillable = [
        'file',
        'type',
        'hash'
    ];
}
