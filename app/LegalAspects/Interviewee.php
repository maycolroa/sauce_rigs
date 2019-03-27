<?php

namespace App\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class Interviewee extends Model
{
    protected $table = 'sau_ct_interviewees';

    public $timestamps = false;

    protected $fillable = [
        'evaluation_id',
        'name',
        'position'
    ];
}