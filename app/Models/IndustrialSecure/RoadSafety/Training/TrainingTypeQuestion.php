<?php

namespace App\Models\IndustrialSecure\RoadSafety\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingTypeQuestion extends Model
{

    protected $table = 'sau_rs_training_type_questions';
    
    protected $fillable = [
        'name',
        'description'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->description,
            'value' => $this->id
        ];
    }
}