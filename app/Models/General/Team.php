<?php

namespace App\Models\General;

use Laratrust\Models\LaratrustTeam;

class Team extends LaratrustTeam
{
    protected $table = 'sau_team';

    protected $fillable = [
        'id', 'name', 'display_name', 'description'
    ];
}
