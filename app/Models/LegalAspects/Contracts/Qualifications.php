<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class Qualifications extends Model
{
    protected $table = 'sau_ct_qualifications';

    protected $fillable = [
        'name',
        'description',
        'fulfillment'
    ];
}
