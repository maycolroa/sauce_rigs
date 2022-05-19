<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{    
    protected $table = 'sau_departaments';

    protected $fillable = ['name', 'code'];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
