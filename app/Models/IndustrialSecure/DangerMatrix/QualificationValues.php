<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class QualificationValues extends Model
{
    protected $table = 'sau_dm_qualification_values';

    protected $fillable = [
        'qualification_type_id',
        'group_by',
        'value',
        'description'
    ];
}
