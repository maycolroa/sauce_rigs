<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class TrainingFiles extends Model
{
    protected $table = 'sau_ct_training_files';
    
    protected $fillable = [
        'training_id',
        'name',
        'file'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "legalAspects/contracts/trainings/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->file}";
    }
}