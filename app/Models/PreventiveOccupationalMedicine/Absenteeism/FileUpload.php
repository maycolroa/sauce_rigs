<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FileUpload extends Model
{

    
    protected $table = "sau_absen_file_upload";

    protected $fillable = ['name','file'];

}
