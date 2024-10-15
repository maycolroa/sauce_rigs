<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ContractEmployeeObservation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_ct_contract_employee_observations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 
        'company_id',
        'user_id',
        'employee_id'
    ];

    /**
     * each tracing is related to one user who wrote it
     * @return App\User
     */
    public function madeBy()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'user_id');
    }
}
