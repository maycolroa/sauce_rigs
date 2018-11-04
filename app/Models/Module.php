<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'sau_modules';

    public $timestamps = false;

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function logMails()
    {
        return $this->hasMany(LogMail::class);
    }
}
