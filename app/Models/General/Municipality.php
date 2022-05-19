<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{    
    protected $table = 'sau_municipalities';

    protected $fillable = ['departament_id', 'name', 'code'];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
