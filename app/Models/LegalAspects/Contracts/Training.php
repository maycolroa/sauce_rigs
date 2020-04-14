<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Training extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_trainings';
    
    protected $fillable = [
        'company_id',
        'name',
        'creator_user',
        'modifier_user',
        'number_questions_show',
        'min_calification',
        'max_calification',
        'number_attemps',
        'active'
    ];

    public function questions()
    {
        return $this->hasMany(TrainingQuestions::class, 'training_id');
    }

     public function activities()
    {
        return $this->belongsToMany(ActivityContract::class, 'sau_ct_training_activity', 'training_id', 'activity_id');
    }

    /*public function files()
    {
        return $this->belongsTo(FileUpload::class, 'sau_ct_training_file', 'file_id', 'training_id');
    }*/

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function toogleState()
    {
        return $this->isActive() ? "NO" : "SI";
    }

    public function scopeByState($query, $state)
    {
        return $query->where('sau_ct_trainings.active', $state);
    }

    /**
     * filters only open/closed check
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  Boleam $isActive
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query, $isActive = true)
    {
        $state = $isActive ? 'SI' : 'NO'; 
        return $query->byState($state);
    }

    public function isActive()
    {
        return $this->state == 'SI';
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