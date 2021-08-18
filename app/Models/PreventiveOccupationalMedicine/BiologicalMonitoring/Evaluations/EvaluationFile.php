<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EvaluationFile extends Model
{
    protected $table = 'sau_bm_evaluation_item_files';

    protected $fillable = [
        'evaluation_id',
        'item_id',
        'name_file',
        'file',
        'type_file'
    ];

    public function evaluation()
    {
        return $this->belongsTo(EvaluationContract::class, 'evaluation_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "preventiveOccupationalMedicine/biologicalMonitoring/evaluations/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->evaluation->company_id}";
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