<?php

namespace App\Models\IndustrialSecure\RoadSafety\Training;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Training extends Model
{
    use CompanyTrait;

    protected $table = 'sau_rs_trainings';
    
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

    public function files()
    {
        return $this->hasMany(TrainingFiles::class, 'training_id');
    }

    public function positions()
    {
        return $this->belongsToMany('App\Models\Administrative\Positions\EmployeePosition', 'sau_rs_training_position', 'training_id', 'employee_position_id');
    }

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
        return $query->where('sau_rs_trainings.active', $state);
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
        return $this->active == 'SI';
    }

}