<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogMail extends Model
{
    protected $table = 'sau_log_mails';

    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
