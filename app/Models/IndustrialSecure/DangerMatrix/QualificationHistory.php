<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class QualificationHistory extends Model
{
    protected $table = 'sau_dm_qualifications_histories';

    protected $fillable = [
        'year',
        'month',
        'type_configuration',
        'value'
    ];
}
