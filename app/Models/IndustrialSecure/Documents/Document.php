<?php

namespace App\Models\IndustrialSecure\Documents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Document extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_documents_security';

    protected $fillable = [
        'name',
        'file'
    ];

    public function users()
    {
        return $this->belongsToMany("app\Models\Administrative\Users\User",'sau_document_security_users', 'document_security_id', 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany("app\Models\Administrative\Roles\Role",'sau_document_security_roles', 'document_security_id', 'role_id');
    }
}
