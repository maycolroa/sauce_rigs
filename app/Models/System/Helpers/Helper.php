<?php

namespace App\Models\System\Helpers;

use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    protected $table = 'sau_helpers';

    protected $fillable = [
        'title',
        'description',
        'module_id'
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\General\Module','module_id');
    }

    public function files()
    {
        return $this->hasMany(HelperFile::class, 'helper_id');
    }
}
