<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ListCheckResumen extends Model
{
    protected $table = 'sau_ct_list_check_resumen';

    protected $fillable = [
        'contract_id',
        'total_standard',
        'total_c',
        'total_nc',
        'total_sc',
        'total_p_c',
        'total_p_nc'
    ];
}