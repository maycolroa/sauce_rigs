<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class EmailBlackList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_email_black_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'problem_type',
        'detail',
        'diagnostic'
    ];
}
