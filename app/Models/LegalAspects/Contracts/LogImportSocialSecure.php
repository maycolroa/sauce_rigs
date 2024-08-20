<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class LogImportSocialSecure extends Model
{    
    use CompanyTrait;
    
    protected $table = 'sau_ct_log_import_social_secure';
    
    protected $fillable = [
        'company_id',
        'user_id',
        'contract_id',
        'description',
        'path_file_social_secure',
        'path_file_employee',
    ];
}