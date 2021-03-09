<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ContractDocument extends Model
{    
    use CompanyTrait;
    
    protected $table = 'sau_ct_contracts_documents';

    public $timestamps = false;
    
    protected $fillable = [
        'company_id',
        'name',
        'document_id'
    ];

    
    /*public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }*/
}