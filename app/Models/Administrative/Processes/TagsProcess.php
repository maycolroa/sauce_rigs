<?php

namespace App\Models\Administrative\Processes;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsProcess extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_tags_processes';

    protected $fillable = [
        'name',
        'company_id',
        'abbreviation'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
