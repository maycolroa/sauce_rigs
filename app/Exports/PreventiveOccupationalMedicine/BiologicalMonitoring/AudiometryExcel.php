<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;

class AudiometryExcel implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
  protected $audiometries;
  public function __construct(Collection $audiometries)
    {
      $this->audiometries = $audiometries;
      
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      return $this->audiometries;
    }

    public function map($audiometries): array
    {
        return [
          Date::dateTimeToExcel(Carbon::createFromFormat('Y-m-d',$audiometries->date)),
          $audiometries->type,
          $audiometries->work_zone_noise,
          $audiometries->previews_events,
          $audiometries->exposition_level,
          $audiometries->left_clasification,
          $audiometries->right_clasification,
          $audiometries->test_score,
          $audiometries->epp,
          $audiometries->air_left_500,
          $audiometries->air_left_1000,
          $audiometries->air_left_2000,
          $audiometries->air_left_3000,
          $audiometries->air_left_4000,
          $audiometries->air_left_6000,
          $audiometries->air_left_8000,
          $audiometries->air_right_500,
          $audiometries->air_right_1000,
          $audiometries->air_right_2000,
          $audiometries->air_right_3000,
          $audiometries->air_right_4000,
          $audiometries->air_right_6000,
          $audiometries->air_right_8000,
          $audiometries->osseous_left_500,
          $audiometries->osseous_left_1000,
          $audiometries->osseous_left_2000,
          $audiometries->osseous_left_3000,
          $audiometries->osseous_left_4000,
          $audiometries->osseous_right_500,
          $audiometries->osseous_right_1000,
          $audiometries->osseous_right_2000,
          $audiometries->osseous_right_3000,
          $audiometries->osseous_right_4000,
          $audiometries->recommendations,
          $audiometries->observation,
          $audiometries->employee_identification,
          $audiometries->employee_name,
          Date::dateTimeToExcel($audiometries->created_at),
          Date::dateTimeToExcel($audiometries->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
          'Fecha',
          'Tipo',
          'Ruido de la zona de trabajo',
          'Eventos Previos',
          'Nivel de exposicion (Disometría)',
          'Clasificación izquierda',
          'Clasificación derecha',
          'Resultado de la prueba',
          'EPP',
          'Aereo Izquierda 500',
          'Aereo Izquierda 1000',
          'Aereo Izquierda 2000',
          'Aereo Izquierda 3000',
          'Aereo Izquierda 4000',
          'Aereo Izquierda 6000',
          'Aereo Izquierda 8000',
          'Aereo Derecha 500',
          'Aereo Derecha 1000',
          'Aereo Derecha 2000',
          'Aereo Derecha 3000',
          'Aereo Derecha 4000',
          'Aereo Derecha 6000',
          'Aereo Derecha 8000',
          'Oseo Izquierda 500',
          'Oseo Izquierda 1000',
          'Oseo Izquierda 2000',
          'Oseo Izquierda 3000',
          'Oseo Izquierda 4000',
          'Oseo Derecha 500',
          'Oseo Derecha 1000',
          'Oseo Derecha 2000',
          'Oseo Derecha 3000',
          'Oseo Derecha 4000',
          'Recomendaciones',
          'Observacion',
          'Identificación empleado',
          'Nombre empleado',
          'Fecha Creacion', 
          'Fecha Actualizacion',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_NUMBER,
            'M' => NumberFormat::FORMAT_NUMBER,
            'N' => NumberFormat::FORMAT_NUMBER,
            'O' => NumberFormat::FORMAT_NUMBER,
            'P' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_NUMBER,
            'R' => NumberFormat::FORMAT_NUMBER,
            'S' => NumberFormat::FORMAT_NUMBER,
            'T' => NumberFormat::FORMAT_NUMBER,
            'U' => NumberFormat::FORMAT_NUMBER,
            'V' => NumberFormat::FORMAT_NUMBER,
            'W' => NumberFormat::FORMAT_NUMBER,
            'X' => NumberFormat::FORMAT_NUMBER,
            'Y' => NumberFormat::FORMAT_NUMBER,
            'Z' => NumberFormat::FORMAT_NUMBER,
            'AA' => NumberFormat::FORMAT_NUMBER,
            'AB' => NumberFormat::FORMAT_NUMBER,
            'AC' => NumberFormat::FORMAT_NUMBER,
            'AD' => NumberFormat::FORMAT_NUMBER,
            'AE' => NumberFormat::FORMAT_NUMBER,
            'AF' => NumberFormat::FORMAT_NUMBER,
            'AG' => NumberFormat::FORMAT_NUMBER,
            'AH' => NumberFormat::FORMAT_NUMBER,
            'AM' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AN' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
