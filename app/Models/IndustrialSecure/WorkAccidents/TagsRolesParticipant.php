<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class TagsRolesParticipant extends Model
{
    protected $table = 'sau_aw_roles_participants_tags';

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
