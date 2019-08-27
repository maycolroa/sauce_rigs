<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class QualificationDanger extends Model
{
    protected $table = 'sau_dm_qualification_danger';

    protected $fillable = [
        'activity_danger_id',
        'type_id',
        'value_id'
    ];

    public $timestamps = false;

    public function typeQualification()
    {
        return $this->belongsTo(QualificationType::class, 'type_id');
    }
}
