<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class FileUpload extends Model
{
    use CompanyTrait;

    protected $table = "sau_absen_file_upload";

    protected $fillable = [
        'name',
        'company_id',
        'path',
        'file',
        'user_id',
        'state',
        'talend_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','user_id');
    }

    public function talend()
    {
        return $this->belongsTo(Talend::class,'talend_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "preventiveOccupationalMedicine/absenteeism/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->company_id}";
    }

    public function path_donwload()
    {
        $pos = strrpos($this->path, 'preventiveOccupationalMedicine');
        $path = substr($this->path, $pos);
        
        return "{$path}{$this->file}";
    }

    public function path_delete($s3 = false)
    {
        if ($s3)
            return $this->path_donwload();
        else
            return "{$this->path}/{$this->file}";
    }
}
