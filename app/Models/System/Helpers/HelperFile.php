<?php

namespace App\Models\System\Helpers;

use Illuminate\Database\Eloquent\Model;

class HelperFile extends Model
{
    protected $table = 'sau_helper_files';

    protected $fillable = [
        'name',
        'helper_id',
        'file'
    ];

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'helper_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "system/helpers/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}1";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->file}";
    }
}
