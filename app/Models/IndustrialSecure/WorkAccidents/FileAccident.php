<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function path_base($storageLocation = true)
    {
        $path = "industrialSecure/accidentWork/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->accident->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->file}";
    }

    public function path_image()
    {
        return Storage::disk('s3')->url($this->path_donwload());
    }

}
