<?php

namespace App\Models\IndustrialSecure\Inspections;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class ConditionReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_inspect_conditions_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'observation',
        'image_1',
        'image_2',
        'image_3',
        'rate',
        'condition_id',
        'user_id',
        'regional_id',
        'headquarter_id',
        'process_id',
        'area_id',
    ];

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'headquarter_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'area_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'process_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User','user_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class)->where([['id', '<>', '98'],['id', '<>', '114']]);
    }

    public function multiselectRate()
    {
        return [
            'name' => $this->rate,
            'value' => $this->id
        ];
    }
}
