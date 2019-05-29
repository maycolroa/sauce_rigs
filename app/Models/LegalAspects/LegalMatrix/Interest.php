<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $table = 'sau_lm_interests';

    protected $fillable = [
        'name',
    ];
}