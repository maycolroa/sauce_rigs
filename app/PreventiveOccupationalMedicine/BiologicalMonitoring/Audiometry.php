<?php

namespace App\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\CompanyTrait;

class Audiometry extends Model
{
    use CompanyTrait;

    protected $table = 'sau_bm_audiometries';

    protected $fillable = [
      'date',
      'type',
      'previews_events',
      'employee_id',
      'exposition_level',
      'air_left_500',
      'air_left_1000',
      'air_left_2000',
      'air_left_3000',
      'air_left_4000',
      'air_left_6000',
      'air_left_8000',
      'air_right_500',
      'air_right_1000',
      'air_right_2000',
      'air_right_3000',
      'air_right_4000',
      'air_right_6000',
      'air_right_8000',
      'osseous_left_500',
      'osseous_left_1000',
      'osseous_left_2000',
      'osseous_left_3000',
      'osseous_left_4000',
      'osseous_right_500',
      'osseous_right_1000',
      'osseous_right_2000',
      'osseous_right_3000',
      'osseous_right_4000',
      'recommendations',
      'observation',
      'epp',
      'created_at',
      'updated_at',
      'gap_left',
      'gap_right',
      'air_left_pta',
      'air_right_pta',
      'osseous_left_pta',
      'osseous_right_pta',
      'severity_grade_air_left_pta',
      'severity_grade_air_right_pta',
      'severity_grade_osseous_left_pta',
      'severity_grade_osseous_right_pta',
      'severity_grade_air_left_4000',
      'severity_grade_air_right_4000',
      'severity_grade_osseous_left_4000',
      'severity_grade_osseous_right_4000',
      'severity_grade_air_left_6000',
      'severity_grade_air_right_6000',
      'severity_grade_air_left_8000',
      'severity_grade_air_right_8000',
      'base_type',
      'base'
    ];

    protected $dates = [
      'created_at',
      'updated_at',
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_employees';

    public function employee(){
        return $this->belongsTo('App\Administrative\Employee','employee_id');
    }

    /**
     * Set the epp separate with comma.
     *
     * @param  string  $value
     * @return void
     */
    public function setEppAttribute($value)
    {
      if($value != null)
      {
        $epp = [];

        if (is_array($value)) //Formulario
        {
          foreach($value as $v)
          {
            array_push($epp,json_decode($v)->value);
          }
          
          $this->attributes['epp'] = implode(",", $epp);
        }
        else //Excel
        {
          $this->attributes['epp'] = implode(",", array_map('trim', explode(",", $value))); //Para eliminar espacios en blanco entre valores
        }
      }
    }
}