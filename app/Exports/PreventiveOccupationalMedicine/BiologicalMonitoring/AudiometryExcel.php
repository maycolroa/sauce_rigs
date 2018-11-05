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
          $audiometries->employee_identification,
          $audiometries->employee_name,
          $audiometries->previews_events,
          $audiometries->exposition_level,
          $audiometries->epp,
          $audiometries->recommendations,
          $audiometries->observation,
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
          $audiometries->gap_left,
          $audiometries->air_left_pta,
          $audiometries->severity_grade_air_left_pta,
          $audiometries->severity_grade_air_left_4000,
          $audiometries->severity_grade_air_left_6000,
          $audiometries->severity_grade_air_left_8000,
          $audiometries->osseous_left_pta,
          $audiometries->severity_grade_osseous_left_pta,
          $audiometries->severity_grade_osseous_left_4000,
          $audiometries->gap_right,
          $audiometries->air_right_pta,
          $audiometries->severity_grade_air_right_pta,
          $audiometries->severity_grade_air_right_4000,
          $audiometries->severity_grade_air_right_6000,
          $audiometries->severity_grade_air_right_8000,
          $audiometries->osseous_right_pta,
          $audiometries->severity_grade_osseous_right_pta,
          $audiometries->severity_grade_osseous_right_4000,
          Date::dateTimeToExcel($audiometries->created_at),
          Date::dateTimeToExcel($audiometries->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
          'Fecha',
          'Identificación empleado',
          'Nombre empleado',
          'Eventos Previos',
          'Nivel de exposición (Disometría)',
          'EPP',
          'Recomendaciones',
          'Observación',
          'Aéreo Izquierda 500 Hz',
          'Aéreo Izquierda 1000 Hz',
          'Aéreo Izquierda 2000 Hz',
          'Aéreo Izquierda 3000 Hz',
          'Aéreo Izquierda 4000 Hz',
          'Aéreo Izquierda 6000 Hz',
          'Aéreo Izquierda 8000 Hz',
          'Aéreo Derecha 500 Hz',
          'Aéreo Derecha 1000 Hz',
          'Aéreo Derecha 2000 Hz',
          'Aéreo Derecha 3000 Hz',
          'Aéreo Derecha 4000 Hz',
          'Aéreo Derecha 6000 Hz',
          'Aéreo Derecha 8000 Hz',
          'Óseo Izquierda 500 Hz',
          'Óseo Izquierda 1000 Hz',
          'Óseo Izquierda 2000 Hz',
          'Óseo Izquierda 3000 Hz',
          'Óseo Izquierda 4000 Hz',
          'Óseo Derecha 500 Hz',
          'Óseo Derecha 1000 Hz',
          'Óseo Derecha 2000 Hz',
          'Óseo Derecha 3000 Hz',
          'Óseo Derecha 4000 Hz',
          'GAP Izquierda',
          'Aéreo Izquierda PTA',
          'Aéreo Grado de severidad Izquierda PTA',
          'Aéreo Grado de severidad Izquierda 4000 Hz',
          'Aéreo Grado de severidad Izquierda 6000 Hz',
          'Aéreo Grado de severidad Izquierda 8000 Hz',
          'Óseo Izquierda PTA',
          'Óseo Grado de severidad Izquierda PTA',
          'Óseo Grado de severidad Izquierda 4000 Hz',
          'GAP Derecha',
          'Aéreo Derecha PTA',
          'Aéreo Grado de severidad Derecha PTA',
          'Aéreo Grado de severidad Derecha 4000 Hz',
          'Aéreo Grado de severidad Derecha 6000 Hz',
          'Aéreo Grado de severidad Derecha 8000 Hz',
          'Óseo Derecha PTA',
          'Óseo Grado de severidad Derecha PTA',
          'Óseo Grado de severidad Derecha 4000 Hz',
          'Fecha creación', 
          'Fecha actualización',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_NUMBER,
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
            'AH' => NumberFormat::FORMAT_NUMBER_00,
            'AM' => NumberFormat::FORMAT_NUMBER_00,
            'AQ' => NumberFormat::FORMAT_NUMBER_00,
            'AV' => NumberFormat::FORMAT_NUMBER_00,
            'AY' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AZ' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
