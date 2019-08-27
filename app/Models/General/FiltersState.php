<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class FiltersState extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_filters_states';

    protected $fillable = [
        'user_id', 'company_id', 'url', 'data'
    ];
}
