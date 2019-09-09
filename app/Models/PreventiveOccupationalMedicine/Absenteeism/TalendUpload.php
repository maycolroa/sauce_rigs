<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TalendUpload extends Model
{

    
    protected $table = "sau_absen_talends";

    protected $fillable = ['route','file'];

}
