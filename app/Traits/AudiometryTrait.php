<?php

namespace App\Traits;

use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use DB;
use Exception;

trait AudiometryTrait
{
    /**
     * Calcula la base de todas las audiometrias del empleado
     *
     * @param Integer $employee_id
     * @return void
     */
    protected function calculateBaseAudiometry($employee_id)
    {
        /**Establecer todos los valores en null */
        DB::table('sau_bm_audiometries')
                ->where('employee_id', $employee_id)
                ->update([
                'base_type' => null, 
                'base' => null,
                'base_state' => null
                ]);

        $audiometries = Audiometry::where('employee_id', $employee_id)->orderBy('date')->get();

        $base_audiometry = null;
        $old_audiometry = null;

        foreach ($audiometries as $key => $new_audiometry)
        {          
            if (!$base_audiometry)
            {
                $this->updateBaseAudiometry($new_audiometry->id, 'Base', null, 'Ninguno');
                $base_audiometry = $new_audiometry;
                $base_audiometry->base_state = 'Ninguno';
                $old_audiometry = $base_audiometry;
            }
            else
            {
                if ($old_audiometry->base_state == 'CUAT')
                {
                    $columns = $this->getColumnsBase();

                    foreach ($columns as $column)
                    {
                        if ( (($old_audiometry->$column - $base_audiometry->$column) >= 15) &&
                            (($new_audiometry->$column - $base_audiometry->$column) >= 15) )
                        {
                            $this->updateBaseAudiometry($base_audiometry->id, 'No base', $base_audiometry->base, $base_audiometry->base_state);
                            $this->updateBaseAudiometry($new_audiometry->id, 'Base', null, 'CUAP');
                            $old_audiometry = $new_audiometry;
                            $old_audiometry->base_type = 'Base';
                            $old_audiometry->base = null;
                            $old_audiometry->base_state = 'CUAP';
                            $base_audiometry = $old_audiometry;
                            continue 2;
                        }
                    }

                    foreach ($columns as $column)
                    {
                        if ( ($new_audiometry->$column - $base_audiometry->$column) >= 15)
                        {
                            $this->updateBaseAudiometry($new_audiometry->id, 'No base', $base_audiometry->id, 'CUAT');
                            $old_audiometry = $new_audiometry;
                            $old_audiometry->base_state = 'CUAT';
                            continue 2;
                        }
                    }

                    $this->updateBaseAudiometry($new_audiometry->id, 'No base', $base_audiometry->id, 'Ninguno');
                    $old_audiometry = $new_audiometry;
                    $old_audiometry->base_state = 'Ninguno';

                }
                else
                {
                    $columns = $this->getColumnsBase();

                    foreach ($columns as $column)
                    {
                        if ( ($new_audiometry->$column - $base_audiometry->$column) >= 15)
                        {
                            $this->updateBaseAudiometry($new_audiometry->id, 'No base', $base_audiometry->id, 'CUAT');
                            $old_audiometry = $new_audiometry;
                            $old_audiometry->base_state = 'CUAT';
                            continue 2;
                        }
                    }

                    $this->updateBaseAudiometry($new_audiometry->id, 'No base', $base_audiometry->id, 'Ninguno');
                    $old_audiometry = $new_audiometry;
                    $old_audiometry->base_state = 'Ninguno';
                }
            }
        }
    }

    /**
     * Actualiza la audiometria, No se utiliza el modelo Eloquent para evitar que se llame nuevamente
     * el oyente por actualizar las columnas de la base
     *
     * @param Integer $audiometry_id
     * @param String $base_type
     * @param String $base
     * @param String $base_state
     * @return void
     */
    private function updateBaseAudiometry($audiometry_id, $base_type, $base, $base_state)
    {
      DB::table('sau_bm_audiometries')
            ->where('id', $audiometry_id)
            ->update([
              'base_type' => $base_type, 
              'base' => $base,
              'base_state' => $base_state
            ]);
    }

    /**
     * Retorna las columnas que seran evaluadas para calcular la base
     *
     * @return Array
    */
    private function getColumnsBase()
    {
        return [
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
        ];
    }
}