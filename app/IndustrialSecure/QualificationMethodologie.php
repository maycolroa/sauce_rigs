<?php

namespace App\IndustrialSecure;

use Illuminate\Database\Eloquent\Model;

class QualificationMethodologie extends Model
{
    protected $table = 'sau_dm_qualification_methodologies';

    protected $fillable = [
        'activity_danger_id',
        'type',
        'qualification'
    ];

    public $timestamps = false;
}
