<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class LawType extends Model
{
    protected $table = 'sau_lm_laws_types';

    protected $fillable = [
        'name',
    ];
}