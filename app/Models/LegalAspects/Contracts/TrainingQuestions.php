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
        'answer',
        'options',
        'value_question',
    ];

    public function Training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->description,
            'value' => $this->id
        ];
    }
}