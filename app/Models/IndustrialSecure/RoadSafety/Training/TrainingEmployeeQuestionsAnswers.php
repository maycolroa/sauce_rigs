<?php

namespace App\Models\IndustrialSecure\RoadSafety\Training;

use Illuminate\Database\Eloquent\Model;

class TrainingEmployeeQuestionsAnswers extends Model
{

    protected $table = 'sau_rs_training_employee_questions_answers';
    
    protected $fillable = [
        'question_id',
        'attempt_id',
        'answers',
        'correct'
    ];

    protected $casts = [
        'correct' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(TrainingQuestions::class, 'question_id');
    }

    public function attempts()
    {
        return $this->hasMany(TrainingEmployeeAttempt::class, 'attempt_id');
    }
}