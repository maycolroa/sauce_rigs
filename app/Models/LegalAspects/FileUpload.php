<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = "sau_ct_file_upload_contracts_leesse";

    protected $fillable = ['name','expirationDate','file'];
}
