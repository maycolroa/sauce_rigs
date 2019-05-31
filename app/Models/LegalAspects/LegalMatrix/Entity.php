<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $table = 'sau_lm_entities';

    protected $fillable = [
        'name',
    ];
}