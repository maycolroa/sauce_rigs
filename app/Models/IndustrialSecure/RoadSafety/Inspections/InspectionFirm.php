<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InspectionFirm extends Model
{   
    const DISK = 's3';

    public $table = 'sau_rs_qualification_inspection_firm';

    protected $fillable = [
        'name',
        'identification',
        'image',
        'qualification_date'
    ];

    public function path_base()
    {
        return "inspections_images/";
    }

    public function path_image($key)
    {
        if ($this->$key && $this->img_exists($key))
            return Storage::disk($this::DISK)->url("{$this->path_base()}{$this->$key}");
    }

    public function img_exists($key)
    {
        return Storage::disk($this::DISK)->exists("{$this->path_base()}{$this->$key}");
    }
    
    public function img_delete($key)
    {
        if ($this->$key && $this->img_exists($key))
           Storage::disk($this::DISK)->delete("{$this->path_base()}{$this->$key}");
    }
}