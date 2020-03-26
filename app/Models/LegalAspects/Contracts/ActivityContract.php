<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ActivityContract extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_activities';
    
    protected $fillable = [
        'company_id',
        'name'
    ];

    public function documents()
    {
        return $this->hasMany(ActivityDocument::class, 'activity_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }
}