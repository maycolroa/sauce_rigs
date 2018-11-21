<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'sau_applications';

    public $timestamps = false;

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
