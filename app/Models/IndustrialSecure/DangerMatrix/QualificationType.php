<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class QualificationType extends Model
{
    protected $table = 'sau_dm_qualification_types';

    protected $fillable = [
        'qualification_id',
        'description'
    ];
    
    public function values()
    {
        return $this->hasMany(QualificationValues::class, 'qualification_type_id');
    }
}
