<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class DangerMatrixActivity extends Model
{
    protected $table = 'sau_danger_matrix_activity';

    protected $fillable = [
        'danger_matrix_id',
        'activity_id',
        'type_activity'
    ];

    public $timestamps = false;

    public function activity()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Activities\Activity', 'activity_id');
    }

    public function dangers()
    {
        return $this->hasMany(ActivityDanger::class, 'dm_activity_id');
    }
}
