<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class LogFilesImport extends Model
{    
    protected $table = 'sau_log_files_import';

    protected $fillable = ['user_id', 'company_id', 'file', 'module'];
}
