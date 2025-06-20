<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $table = 'sau_keywords';

    protected $fillable = ['name', 'display_name'];

    public function multiselect()
    {
        return [
            'name' => $this->display_name,
            'value' => $this->id
        ];
    }
}
