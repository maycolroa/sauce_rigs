<?php

namespace App\Observers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;

class AudiometryObserver
{
    /**
     * Handle the audiometry "saving" event.
     *
     * @param  \App\Audiometry  $audiometry
     * @return void
     */
    public function saving(Audiometry $audiometry)
    {
      $audiometry->gap_left = $this->GapLeft($audiometry);
      $audiometry->gap_right = $this->GapRight($audiometry);
      $audiometry->air_left_pta = $this->AirLeftPta($audiometry);
      $audiometry->air_right_pta = $this->AirRightPta($audiometry);
      $audiometry->osseous_left_pta = $this->OsseousLeftPta($audiometry);
      $audiometry->osseous_right_pta = $this->OsseousRightPta($audiometry);
      $audiometry->severity_grade_air_left_pta = $this->SeverityGradeAirLeftPta($audiometry);
      $audiometry->severity_grade_air_right_pta = $this->SeverityGradeAirRightPta($audiometry);
      $audiometry->severity_grade_osseous_left_pta = $this->SeverityGradeOsseousLeftPta($audiometry);
      $audiometry->severity_grade_osseous_right_pta = $this->SeverityGradeOsseousRightPta($audiometry);
      $audiometry->severity_grade_air_left_4000 = $this->SeverityGradeAirLeft4000($audiometry);
      $audiometry->severity_grade_air_right_4000 = $this->SeverityGradeAirRight4000($audiometry);
      $audiometry->severity_grade_osseous_left_4000 = $this->SeverityGradeOsseousLeft4000($audiometry);
      $audiometry->severity_grade_osseous_right_4000 = $this->SeverityGradeOsseousRight4000($audiometry);
      $audiometry->severity_grade_air_left_6000 = $this->SeverityGradeAirLeft6000($audiometry);
      $audiometry->severity_grade_air_right_6000 = $this->SeverityGradeAirRight6000($audiometry);
      $audiometry->severity_grade_air_left_8000 = $this->SeverityGradeAirLeft8000($audiometry);
      $audiometry->severity_grade_air_right_8000 = $this->SeverityGradeAirRight8000($audiometry);
      $base = $this->BasePta($audiometry);
      $audiometry->base_type = $base[0];
      $audiometry->base      = $base[1];
    }

      
    /**
     * Metodo para el atributo gap_left, define si una audiometria es gap, no gap, o no aplica
     *
     * @return String
     */
    protected function GapLeft($audiometry){
      if($audiometry->osseous_left_500 || $audiometry->osseous_left_1000 || $audiometry->osseous_left_2000 || $audiometry->osseous_left_3000 || $audiometry->osseous_left_4000){
        if(($audiometry->air_left_500 - $audiometry->osseous_left_500) > 5 || ($audiometry->air_left_500 - $audiometry->osseous_left_500) < -5){
          return "GAP";
        }
        if(($audiometry->air_left_1000 - $audiometry->osseous_left_1000) > 5 || ($audiometry->air_left_1000 - $audiometry->osseous_left_1000) < -5){
          return "GAP";
        }
        if(($audiometry->air_left_2000 - $audiometry->osseous_left_2000) > 5 || ($audiometry->air_left_2000 - $audiometry->osseous_left_2000) < -5){
          return "GAP";
        }
        if(($audiometry->air_left_3000 - $audiometry->osseous_left_3000) > 5 || ($audiometry->air_left_3000 - $audiometry->osseous_left_3000) < -5){
          return "GAP";
        }
        if(($audiometry->air_left_4000 - $audiometry->osseous_left_4000) > 5 || ($audiometry->air_left_4000 - $audiometry->osseous_left_4000) < -5){
          return "GAP";
        }
        return "No GAP";
      }
      return "No aplica";
    }
    
    /**
     * Metodo para el atributo gap_right, define si una audiometria es gap, no gap, o no aplica
     *
     * @return String
     */
    protected function GapRight($audiometry){
      if($audiometry->osseous_right_500 || $audiometry->osseous_right_1000 || $audiometry->osseous_right_2000 || $audiometry->osseous_right_3000 || $audiometry->osseous_right_4000){
        if(($audiometry->air_right_500 - $audiometry->osseous_right_500) > 5 || ($audiometry->air_right_500 - $audiometry->osseous_right_500) < -5){
          return "GAP";
        }
        if(($audiometry->air_right_1000 - $audiometry->osseous_right_1000) > 5 || ($audiometry->air_right_1000 - $audiometry->osseous_right_1000) < -5){
          return "GAP";
        }
        if(($audiometry->air_right_2000 - $audiometry->osseous_right_2000) > 5 || ($audiometry->air_right_2000 - $audiometry->osseous_right_2000) < -5){
          return "GAP";
        }
        if(($audiometry->air_right_3000 - $audiometry->osseous_right_3000) > 5 || ($audiometry->air_right_3000 - $audiometry->osseous_right_3000) < -5){
          return "GAP";
        }
        if(($audiometry->air_right_4000 - $audiometry->osseous_right_4000) > 5 || ($audiometry->air_right_4000 - $audiometry->osseous_right_4000) < -5){
          return "GAP";
        }
        return "No GAP";
      }
      return "No aplica";
    }
    
    /**
     * Metodo para el atributo append air_left_pta, se calcula el promedio de las air left 500 - 3000
     *
     * @return String
     */
    protected function AirLeftPta($audiometry){
      return ($audiometry->air_left_500 + $audiometry->air_left_1000 + $audiometry->air_left_2000 + $audiometry->air_left_3000) / 4;
    }
    
    /**
     * Metodo para el atributo append air_right_pta, se calcula el promedio de las air right 500 - 3000
     *
     * @return String
     */
    protected function AirRightPta($audiometry){
      return ($audiometry->air_right_500 + $audiometry->air_right_1000 + $audiometry->air_right_2000 + $audiometry->air_right_3000) / 4;
    }
    
    /**
     * Metodo para el atributo append osseous_left_pta, se calcula el promedio de las osseous left 500 - 3000
     *
     * @return String
     */
    protected function OsseousLeftPta($audiometry){
      if($audiometry->osseous_left_500 == null && $audiometry->osseous_left_1000 == null && $audiometry->osseous_left_2000 == null && $audiometry->osseous_left_3000 == null){
        return null;
      }
      return ($audiometry->osseous_left_500 + $audiometry->osseous_left_1000 + $audiometry->osseous_left_2000 + $audiometry->osseous_left_3000) / 4;
    }
    
    /**
     * Metodo para el atributo append osseous_right_pta, se calcula el promedio de las osseous right 500 - 3000
     *
     * @return String
     */
    protected function OsseousRightPta($audiometry){
      if($audiometry->osseous_right_500 == null && $audiometry->osseous_right_1000 == null && $audiometry->osseous_right_2000 == null && $audiometry->osseous_right_3000 == null){
        return null;
      }
      return ($audiometry->osseous_right_500 + $audiometry->osseous_right_1000 + $audiometry->osseous_right_2000 + $audiometry->osseous_right_3000) / 4;
    }
    
    /**
     * Metodo para el atributo append severity_grade_air_left_pta
     *
     * @return String
     */
    protected function SeverityGradeAirLeftPta($audiometry){
      return $this->gradeSeverity($audiometry->air_left_pta);
    }

    /**
     * Metodo para el atributo append severity_grade_air_right_pta
     *
     * @return String
     */
    protected function SeverityGradeAirRightPta($audiometry){
      return $this->gradeSeverity($audiometry->air_right_pta);
    }

    /**
     * Metodo para el atributo append severity_grade_osseous_left_pta
     *
     * @return String
     */
    protected function SeverityGradeOsseousLeftPta($audiometry){
      return $this->gradeSeverity($audiometry->osseous_left_pta);
    }

    /**
     * Metodo para el atributo append severity_grade_osseous_right_pta
     *
     * @return String
     */
    protected function SeverityGradeOsseousRightPta($audiometry){
      return $this->gradeSeverity($audiometry->osseous_right_pta);
    }

    /**
     * Metodo para el atributo append severity_grade_air_left_4000
     *
     * @return String
     */
    protected function SeverityGradeAirLeft4000($audiometry){
      return $this->gradeSeverity($audiometry->air_left_4000);
    }

    /**
     * Metodo para el atributo append severity_grade_air_right_4000
     *
     * @return String
     */
    protected function SeverityGradeAirRight4000($audiometry){
      return $this->gradeSeverity($audiometry->air_right_4000);
    }

    /**
     * Metodo para el atributo append severity_grade_osseous_left_4000
     *
     * @return String
     */
    protected function SeverityGradeOsseousLeft4000($audiometry){
      return $this->gradeSeverity($audiometry->osseous_left_4000);
    }

    /**
     * Metodo para el atributo append severity_grade_osseous_right_4000
     *
     * @return String
     */
    protected function SeverityGradeOsseousRight4000($audiometry){
      return $this->gradeSeverity($audiometry->osseous_right_4000);
    }

    /**
     * Metodo para el atributo append severity_grade_air_left_6000
     *
     * @return String
     */
    protected function SeverityGradeAirLeft6000($audiometry){
      return $this->gradeSeverity($audiometry->air_left_6000);
    }

    /**
     * Metodo para el atributo append severity_grade_air_right_6000
     *
     * @return String
     */
    protected function SeverityGradeAirRight6000($audiometry){
      return $this->gradeSeverity($audiometry->air_right_6000);
    }

    /**
     * Metodo para el atributo append severity_grade_air_left_8000
     *
     * @return String
     */
    protected function SeverityGradeAirLeft8000($audiometry){
      return $this->gradeSeverity($audiometry->air_left_8000);
    }
  
    /**
     * Metodo para el atributo append severity_grade_air_right_8000
     *
     * @return String
     */
    protected function SeverityGradeAirRight8000($audiometry){
      return $this->gradeSeverity($audiometry->air_right_8000);
    }

    /**
     * retorna el nivel de audicion para la frecuencia ingresada
     *
     * @param Int $frecuency
     * @return String
     */
    private function gradeSeverity($frecuency){
      
      if($frecuency == null){
        return null;
      }
      else if($frecuency <= 25){
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

    /**
     * Metodo para el atributo base_type y base
     */

     private function BasePta($audiometry)
     { 
        $col_base_type = "base_type";
        $audiometry_base = Audiometry::where('employee_id', $audiometry->employee_id)->where('base_type', 'Base')->first();
        
        if (!$audiometry_base)
          return ['Base', null];

        $base_level = $this->levelPTA($audiometry_base->severity_grade_air_left_pta) + $this->levelPTA($audiometry_base->severity_grade_air_right_pta);
        $new_level = $this->levelPTA($audiometry->severity_grade_air_left_pta) + $this->levelPTA($audiometry->severity_grade_air_right_pta);

        if ($base_level >= $new_level)
        {
          return ['No base', $audiometry_base->id];
        }
        else
        {
          $audiometry_base->base_type = 'No base';
          $audiometry_base->unsetEventDispatcher();
          $audiometry_base->save();
          return ['Base', null];
        }
     }

     private function levelPTA($value)
     {
        if ($value == 'Audición normal')
          return 1;
        else if ($value == 'Hipoacusia leve')
          return 2;
        else if ($value == 'Hipoacusia moderada')
          return 3;
        else if ($value == 'Hipoacusia moderada a severa')
          return 4;
        else if ($value == 'Hipoacusia severa')
          return 5;
        else if ($value == 'Hipoacusia profunda')
          return 6;
        else 0;
     }
}
