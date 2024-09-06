<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FileModuleState extends Model
{    
    protected $table = "sau_ct_file_module_state";

    protected $fillable = [
        'contract_id',
        'file_id',
        'module',
        'state', 
        'date'
    ];

    //the attribute define the table for scope company execute
    
}
