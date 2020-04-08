<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class TrainingTypeQuestion extends Model
{

    protected $table = 'sau_ct_training_types_questions';
    
    protected $fillable = [
        'name'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}