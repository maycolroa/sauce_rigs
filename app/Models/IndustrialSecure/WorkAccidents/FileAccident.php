<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class FileAccident extends Model
{
    //use CompanyTrait;

    protected $table = 'sau_aw_form_accidents_files';

    protected $fillable = [
        'name',
        'file',
        'type',
        'form_accident_id'
    ];

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'form_accident_id');
    }
}
