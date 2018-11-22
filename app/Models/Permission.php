<?php

namespace App\Models;

use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
{
    protected $fillable = [
        'name', 'display_name', 'description', 'module_id'
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'module_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->display_name,
            'value' => $this->name
        ];
    }
}
