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

    public function dependencies()
    {
        return $this->belongsToMany(Module::class, 'sau_module_dependencies', 'module_id', 'module_dependence_id');
    }

    public function multiselect()
    {
        $display_name = explode("/", $this->display_name);
        $display_name = COUNT($display_name) > 1 ? $display_name[1] : $display_name[0];

        return [
          'name' => $display_name,
          'value' => $this->id
        ];
    }

    public function scopeMain($query)
    {
        return $query->where("sau_modules.main", "SI");
    }

    public function isMain()
    {
        return $this->main == 'SI';
    }
}
