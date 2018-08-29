<?php

namespace App\BiologicalMonitoring;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\CompanyTrait;

class Audiometry extends Model
{
    use CompanyTrait;

    protected $table = 'bm_audiometries';

    protected $fillable = [
        'date',
        'type',
        'previews_events',
        'employee_id',
        'work_zone_noise',
        'exposition_level',
        'left_500',
        'left_1000',
        'left_2000',
        'left_3000',
        'left_4000',
        'left_6000',
        'left_8000',
        'right_500',
        'right_1000',
        'right_2000',
        'right_3000',
        'right_4000',
        'right_6000',
        'right_8000',
        'left_clasification',
        'right_clasification',
        'recommendations',
        'obs',
        'test_score',
        'epp',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company = 'sau_employees';

    /*public function getdateAttribute($value){
        return (Carbon::createFromFormat('D, d M Y H:i:s e',$value))->format('Ymd');
    }*/

    public function employee(){
        return $this->belongsTo('App\Administrative\Employee','employee_id');
    }
}
