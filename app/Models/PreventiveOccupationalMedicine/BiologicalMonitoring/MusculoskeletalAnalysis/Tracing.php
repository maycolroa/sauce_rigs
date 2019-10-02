<?php

namespace App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Tracing extends Model
{
    use CompanyTrait;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_bm_tracings';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'description', 
        'identification',
        'user_id'
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
