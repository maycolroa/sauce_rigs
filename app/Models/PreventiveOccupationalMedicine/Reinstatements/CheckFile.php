<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class CheckFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_files';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file', 'file_name', 'check_id', 'user_id'
    ];

    /**
     * each tracing is related to one user who wrote it
     * @return App\User
     */
    public function madeBy()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'user_id');
    }

    /**
     * returns the restriction model related to this check
     * @return App\Check
     */
    public function check()
    {
        return $this->belongsTo(Check::class, 'check_id');
    }
}
