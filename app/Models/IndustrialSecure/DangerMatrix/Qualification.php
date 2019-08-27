<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $table = 'sau_dm_qualifications';

    protected $fillable = [
        'name'
    ];

    public function types()
    {
        return $this->hasMany(QualificationType::class, 'qualification_id');
    }
}
