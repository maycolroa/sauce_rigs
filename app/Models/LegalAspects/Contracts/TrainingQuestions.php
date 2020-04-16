<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class TrainingQuestions extends Model
{
    protected $table = 'sau_ct_training_questions';
    
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
        return $this->belongsTo(TrainingTypeQuestion::class, 'type_question_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->description,
            'value' => $this->id
        ];
    }
}