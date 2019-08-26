<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ListCheckChangeHistory extends Model
{
    protected $table = 'sau_ct_lisk_check_change_histories';

    protected $fillable = [
        'contract_id',
        'user_id'
    ];
}