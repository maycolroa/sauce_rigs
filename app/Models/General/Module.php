<?php

namespace App\Models\General;

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
        return $this->hasMany('App\Models\System\LogMails\LogMail');
    }

    public function licenses()
    {
        return $this->belongsToMany('App\Models\System\Licenses\License', 'sau_license_module');
    }

    public function multiselect()
    {
        return [
          'name' => $this->display_name,
          'value' => $this->id
        ];
    }
}
