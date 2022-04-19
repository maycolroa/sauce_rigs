<?php

namespace App\Models\PreventiveOccupationalMedicine\Documents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Document extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_documents_preventive';

    protected $fillable = [
        'name',
        'file'
    ];

    public function users()
    {
        return $this->belongsToMany("app\Models\Administrative\Users\User",'sau_documents_preventive_user', 'document_preventive_id', 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany("app\Models\Administrative\Roles\Role",'sau_documents_preventive_roles', 'document_preventive_id', 'role_id');
    }
}
