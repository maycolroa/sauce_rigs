<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\UtilsTrait;

class WarningImportTemplate implements FromCollection, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $data;

    public function __construct()
    {
      $this->data = collect([]);

      $warning = [
        "No debe cambiar la estructura del archivo, por ejemplo: \n\n *Cambiar las columnas de lugar. \n *Ocultar filas o columnas. \n\n Esto puede llegar a generar errores al momento de la carga y procesamiento de la información suministrada.",
        "No debe modificar los datos suministrados por el sistema en las pestañas adicionales, por ejemplo: \n\n *Cambiar los codigos dados por el sistema, ya que esta información que modifique o agregue SAUCE no podra procesarla y provocara un error al momento de procesar la información.",
        "Debe leer con atencion la informacion entre parentesis en los titulos de las columnas ya que esta brinda indicaciones sobre los datos que se deben cargar, tales como: \n\n *Si la informacion es requerida u opcional, si es requerida la informacion de mostrara un asterisco entre parentesis de esta forma (*).\n *Indicaciones de donde puede encontrar la información para llenar los datos o los valores validas para cada columna, \n si es una columna dependiente de otras opciones indicara la pestaña y columna de la cual debe obtener los datos necesarios,\n  *Si la columna requiere valores especificos se indicara cuales son estos por ejemplo (SI, NO), (Masculino, Femenino, Sin sexo), entre otras.\n\n  De no seguir estas indicaciones SAUCE puede no reconocer valores indicados y marcar la fila como con informacion erronea o no procesar para nada los datos cargados"
      ];

      foreach ($warning as $key => $value)
        {
            $this->data->push(['leyend'=>$value]);
        }
    }

    public function collection()
    {
      return $this->data;
    }

    public function map($data): array
    {
      $result = [];

      foreach ($data as $key => $value)
      {
        array_push($result, $value);
      }
      
      return $result;
    }


    public function headings(): array
    {
        return [
            'Indicaciones'
        ];
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Indicaciones (Importante)';
    }

    public static function afterSheet(AfterSheet $event)
    {
      $sheet = $event->sheet;
      $worksheet = $sheet->getDelegate();

      $red = "d9534f";
      $white = 'FFFFFF';

      $sheet->styleCells(
        'A1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
               'name' => 'Arial', 
               'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => $white
                ],
                'endColor' => [
                    'rgb' => $red
                ],
            ]
          ]
      );

      // Ajuste automático del ancho de la columna A
      $worksheet->getColumnDimension('A')->setAutoSize(true);

      // Ajuste de texto para que se ajuste al ancho de la columna A
      $worksheet->getStyle('A1:A7')->getAlignment()->setWrapText(true);

      // Ajuste automático del alto de las filas
      $highestRow = $worksheet->getHighestRow();
      for ($i = 1; $i <= $highestRow; $i++) {
          $worksheet->getRowDimension($i)->setRowHeight(-1); //Set the row height to auto size.
      }
    }
}