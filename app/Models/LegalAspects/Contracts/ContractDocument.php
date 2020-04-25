<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ContractDocument extends Model
{    
    protected $table = 'sau_ct_contracts_documents';

    public $timestamps = false;
    
    protected $fillable = [
        'contract_id',
        'name'
    ];

    
    /*public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }*/
}