<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class TrainingTypeQuestion extends Model
{

    protected $table = 'sau_ct_training_types_questions';
    
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