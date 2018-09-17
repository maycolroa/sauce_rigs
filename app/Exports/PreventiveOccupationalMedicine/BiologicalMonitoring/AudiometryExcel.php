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
          $audiometries->left_500,
          $audiometries->left_1000,
          $audiometries->left_2000,
          $audiometries->left_3000,
          $audiometries->left_4000,
          $audiometries->left_6000,
          $audiometries->left_8000,
          $audiometries->right_500,
          $audiometries->right_1000,
          $audiometries->right_2000,
          $audiometries->right_3000,
          $audiometries->right_4000,
          $audiometries->right_6000,
          $audiometries->right_8000,
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
          'Izquierda 500',
          'Izquierda 1000',
          'Izquierda 2000',
          'Izquierda 3000',
          'Izquierda 4000',
          'Izquierda 6000',
          'Izquierda 8000',
          'Derecha 500',
          'Derecha 1000',
          'Derecha 2000',
          'Derecha 3000',
          'Derecha 4000',
          'Derecha 6000',
          'Derecha 8000',
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
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AB' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AC' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
