<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class ChangeHistory extends Model
{
    protected $table = 'sau_dm_change_histories';

    protected $fillable = [
        'danger_matrix_id',
        'user_id',
        'description'
    ];
}
