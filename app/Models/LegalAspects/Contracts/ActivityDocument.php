<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class ActivityDocument extends Model
{
    protected $table = 'sau_ct_activities_documents';

    public $timestamps = false;
    
    protected $fillable = [
        'activity_id',
        'name',
        'type'
    ];


    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}