<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use App\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use CompanyTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_absen_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_show',
        'name_report',
        'user',
        'site',
        'company_id',
        'state',
        'type',
        'es_bsc',
        'module_id',
    ];

    public function users()
    {
        return $this->belongsToMany("app\Models\Administrative\Users\User",'sau_absen_report_user', 'report_id', 'user_id');
    }
}