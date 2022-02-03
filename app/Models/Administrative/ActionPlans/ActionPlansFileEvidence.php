<?php

namespace App\Models\Administrative\ActionPlans;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ActionPlansFileEvidence extends Model
{
    protected $table = 'sau_action_plans_files_evidences';

    protected $fillable = [
        'activity_id',
        'file'
    ];

    public function activity()
    {
        return $this->belongsTo(ActionPlansActivity::class, 'activity_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "administrative/actionPlan/evidences/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->activity->company_id}";
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