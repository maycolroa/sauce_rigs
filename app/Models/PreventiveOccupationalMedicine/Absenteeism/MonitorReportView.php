<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use App\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class MonitorReportView extends Model
{
    use CompanyTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_absen_monitor_report_views';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'report_id'
    ];

    /**
     * Código para poder ver los informes de tableau
     * @var string
     */
    private $PostCode;

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','user_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class,'report_id');
    }
}