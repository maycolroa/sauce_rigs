<?php

namespace App\Models\IndustrialSecure\RoadSafety\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingQuestions extends Model
{
    protected $table = 'sau_rs_training_questions';
    
    protected $fillable = [
        'training_id',
        'description',
        'type_question_id',
        'answer_options'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'answer_options' => 'collection',
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\LegalAspects\Contracts\TrainingTypeQuestion', 'type_question_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->description,
            'value' => $this->id
        ];
    }
}