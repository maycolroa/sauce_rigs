<?php

namespace App\Models\LegalAspects\Documents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Document extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_documents_legals';

    protected $fillable = [
        'name',
        'file'
    ];

    public function users()
    {
        return $this->belongsToMany("app\Models\Administrative\Users\User",'sau_documents_legals_user', 'document_legal_id', 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany("app\Models\Administrative\Roles\Role",'sau_documents_legals_roles', 'document_legal_id', 'role_id');
    }
}
