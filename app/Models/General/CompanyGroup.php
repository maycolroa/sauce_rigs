<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class CompanyGroup extends Model
{
    //use CompanyTrait;
    
    protected $table = 'sau_company_groups';

    protected $fillable = ['name', 'active', 'receive_report', 'emails'];

    public function company()
    {
        return $this->belongsToMany(Company::class, 'company_group_id');
    }

    public function multiselect()
    {
        return [
          'name' => $this->name,
          'value' => $this->id
        ];
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->active == 'SI';
    }
}
