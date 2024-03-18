<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QualificationRepeat extends Model
{
    protected $table = "sau_rs_inspection_qualification_repeat";

    protected $fillable = [
        'inspection_id',
        'user_id',
        'regional',
        'headquarter',
        'process',
        'area',
        'fields_adds',
        'send_emails',
        'qualification_date',
        'repeat_date'
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'user_id');
    }
}
