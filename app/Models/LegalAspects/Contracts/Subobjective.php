<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class Subobjective extends Model
{
    protected $table = 'sau_ct_subobjectives';

    protected $fillable = [
        'objective_id',
        'description'
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'subobjective_id');
    }

    public function objective()
    {
        return $this->belongsTo(Objective::class, 'objective_id');
    }
}