<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class LaborNotes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_labor_notes';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 
        'check_id',
        'user_id',
        'role_visor'
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
