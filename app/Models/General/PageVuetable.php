<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class PageVuetable extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_pages_vuetable';

    protected $fillable = [
        'user_id', 'company_id', 'vuetable', 'page'
    ];
}
