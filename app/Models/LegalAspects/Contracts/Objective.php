<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    protected $table = 'sau_ct_objectives';

    protected $fillable = [
        'evaluation_id',
        'description'
    ];

    public function subobjectives()
    {
        return $this->hasMany(Subobjective::class, 'objective_id');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }
}