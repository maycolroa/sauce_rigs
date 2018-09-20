<?php

namespace App\PreventiveOccupationalMedicine\BiologicalMonitoring;

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
        'left_clasification',
        'right_clasification',
        'recommendations',
        'observation',
        'test_score',
        'epp',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
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
      if($value != null){
        $epp = [];
        foreach($value as $v){
          array_push($epp,json_decode($v)->value);
        }
          $this->attributes['epp'] = implode(",", $epp);
      }
    }
    
    /**
     * Accessor para el atributo gap_left, define si una audiometria es gap, no gap, o no aplica
     *
     * @return String
     */
    public function getGapLeftAttribute(){
      if($this->osseous_left_500 || $this->osseous_left_1000 || $this->osseous_left_2000 || $this->osseous_left_3000 || $this->osseous_left_4000){
        if(($this->air_left_500 - $this->osseous_left_500) > 5 || ($this->air_left_500 - $this->osseous_left_500) < -5){
          return "GAP";
        }
        if(($this->air_left_1000 - $this->osseous_left_1000) > 5 || ($this->air_left_1000 - $this->osseous_left_1000) < -5){
          return "GAP";
        }
        if(($this->air_left_2000 - $this->osseous_left_2000) > 5 || ($this->air_left_2000 - $this->osseous_left_2000) < -5){
          return "GAP";
        }
        if(($this->air_left_3000 - $this->osseous_left_3000) > 5 || ($this->air_left_3000 - $this->osseous_left_3000) < -5){
          return "GAP";
        }
        if(($this->air_left_4000 - $this->osseous_left_4000) > 5 || ($this->air_left_4000 - $this->osseous_left_4000) < -5){
          return "GAP";
        }
        return "No GAP";
      }
      return "No aplica";
    }
    
    /**
     * Accessor para el atributo gap_right, define si una audiometria es gap, no gap, o no aplica
     *
     * @return String
     */
    public function getGapRightAttribute(){
      if($this->osseous_right_500 || $this->osseous_right_1000 || $this->osseous_right_2000 || $this->osseous_right_3000 || $this->osseous_right_4000){
        if(($this->air_right_500 - $this->osseous_right_500) > 5 || ($this->air_right_500 - $this->osseous_right_500) < -5){
          return "GAP";
        }
        if(($this->air_right_1000 - $this->osseous_right_1000) > 5 || ($this->air_right_1000 - $this->osseous_right_1000) < -5){
          return "GAP";
        }
        if(($this->air_right_2000 - $this->osseous_right_2000) > 5 || ($this->air_right_2000 - $this->osseous_right_2000) < -5){
          return "GAP";
        }
        if(($this->air_right_3000 - $this->osseous_right_3000) > 5 || ($this->air_right_3000 - $this->osseous_right_3000) < -5){
          return "GAP";
        }
        if(($this->air_right_4000 - $this->osseous_right_4000) > 5 || ($this->air_right_4000 - $this->osseous_right_4000) < -5){
          return "GAP";
        }
        return "No GAP";
      }
      return "No aplica";
    }
    
    /**
     * Accessor para el atributo append air_left_pta, se calcula el promedio de las air left 500 - 3000
     *
     * @return String
     */
    public function getAirLeftPtaAttribute(){
      return ($this->air_left_500 + $this->air_left_1000 + $this->air_left_2000 + $this->air_left_3000) / 4;
    }
    
    /**
     * Accessor para el atributo append air_right_pta, se calcula el promedio de las air right 500 - 3000
     *
     * @return String
     */
    public function getAirRightPtaAttribute(){
      return ($this->air_right_500 + $this->air_right_1000 + $this->air_right_2000 + $this->air_right_3000) / 4;
    }
    
    /**
     * Accessor para el atributo append osseous_left_pta, se calcula el promedio de las osseous left 500 - 3000
     *
     * @return String
     */
    public function getOsseousLeftPtaAttribute(){
      return ($this->osseous_left_500 + $this->osseous_left_1000 + $this->osseous_left_2000 + $this->osseous_left_3000) / 4;
    }
    
    /**
     * Accessor para el atributo append osseous_right_pta, se calcula el promedio de las osseous right 500 - 3000
     *
     * @return String
     */
    public function getOsseousRightPtaAttribute(){
      return ($this->osseous_right_500 + $this->osseous_right_1000 + $this->osseous_right_2000 + $this->osseous_right_3000) / 4;
    }
    
    /**
     * Accessor para el atributo append severity_grade_air_left_pta
     *
     * @return String
     */
    public function getSeverityGradeAirLeftPtaAttribute(){
      return $this->gradeSeverity($this->air_left_pta);
    }

    /**
     * Accessor para el atributo append severity_grade_air_right_pta
     *
     * @return String
     */
    public function getSeverityGradeAirRightPtaAttribute(){
      return $this->gradeSeverity($this->air_right_pta);
    }

    /**
     * Accessor para el atributo append severity_grade_osseous_left_pta
     *
     * @return String
     */
    public function getSeverityGradeOsseousLeftPtaAttribute(){
      return $this->gradeSeverity($this->osseous_left_pta);
    }

    /**
     * Accessor para el atributo append severity_grade_osseous_right_pta
     *
     * @return String
     */
    public function getSeverityGradeOsseousRightPtaAttribute(){
      return $this->gradeSeverity($this->osseous_right_pta);
    }

    /**
     * Accessor para el atributo append severity_grade_air_left_4000
     *
     * @return String
     */
    public function getSeverityGradeAirLeft4000Attribute(){
      return $this->gradeSeverity($this->air_left_4000);
    }

    /**
     * Accessor para el atributo append severity_grade_air_right_4000
     *
     * @return String
     */
    public function getSeverityGradeAirRight4000Attribute(){
      return $this->gradeSeverity($this->air_right_4000);
    }

    /**
     * Accessor para el atributo append severity_grade_osseous_left_4000
     *
     * @return String
     */
    public function getSeverityGradeOsseousLeft4000Attribute(){
      return $this->gradeSeverity($this->ossesous_left_4000);
    }

    /**
     * Accessor para el atributo append severity_grade_osseous_right_4000
     *
     * @return String
     */
    public function getSeverityGradeOsseousRight4000Attribute(){
      return $this->gradeSeverity($this->ossesous_right_4000);
    }

    /**
     * Accessor para el atributo append severity_grade_air_left_6000
     *
     * @return String
     */
    public function getSeverityGradeAirLeft6000Attribute(){
      return $this->gradeSeverity($this->air_left_6000);
    }

    /**
     * Accessor para el atributo append severity_grade_air_right_6000
     *
     * @return String
     */
    public function getSeverityGradeAirRight6000Attribute(){
      return $this->gradeSeverity($this->air_right_6000);
    }

    /**
     * Accessor para el atributo append severity_grade_air_left_8000
     *
     * @return String
     */
    public function getSeverityGradeAirLeft8000Attribute(){
      return $this->gradeSeverity($this->air_left_8000);
    }
  
    /**
     * Accessor para el atributo append severity_grade_air_right_8000
     *
     * @return String
     */
    public function getSeverityGradeAirRight8000Attribute(){
      return $this->gradeSeverity($this->air_right_8000);
    }

    /**
     * retorna el nivel de audicion para la frecuencia ingresada
     *
     * @param Int $frecuency
     * @return String
     */
    private function gradeSeverity($frecuency){
      if($frecuency <= 25){
        return "Audición normal";
      }
      else if($frecuency >= 26 && $frecuency <= 40){
        return "Hipoacusia leve";
      }
      else if($frecuency >= 41 && $frecuency <= 55){
        return "Hipoacusia moderada";
      }
      else if($frecuency >= 56 && $frecuency <= 70){
        return "Hipoacusia moderada a severa";
      }
      else if($frecuency >= 71 && $frecuency <= 90){
        return "Hipoacusia severa";
      }
      else{
        return "Hipoacusia profunda";
      }
    }
}