<?php

namespace App\Models\IndustrialSecure\RoadSafety;

use Illuminate\Database\Eloquent\Model;

class PositionDocument extends Model
{
    protected $table = 'sau_rs_position_documents';

    //public $timestamps = false;
    
    protected $fillable = [
        'position_id',
        'name'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}