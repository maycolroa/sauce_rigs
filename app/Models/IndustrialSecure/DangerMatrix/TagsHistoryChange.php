<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsHistoryChange extends Model
{
    use CompanyTrait;

    protected $table = 'sau_tags_dm_history_change';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}
