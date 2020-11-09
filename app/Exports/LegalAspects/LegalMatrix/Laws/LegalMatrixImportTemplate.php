<?php

namespace App\Exports\LegalAspects\LegalMatrix\Laws;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use App\Traits\UtilsTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class LegalMatrixImportTemplate implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $data;
    protected $company_id;

    public function __construct($data, $company_id)
    {
      $this->data = $data;
      $this->company_id = $company_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
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
      $columns = [
        'Nombre(*)',
        'Número(*)',
        'Tipo (Los posibles valores se encuentran en la pestaña Tipos de Leyes)(*)',
        'Año(*)',
        'Sistema que aplica(*)',
        'Descripción(*)',
        'Observación',
        'Tema ambiental (Los posibles valores se encuentran en la pestaña Temas ambientales)(*)',
        'Ente(Los posibles valores se encuentran en la pestaña Entes)(*)',
        'Tema SST (Los posibles valores se encuentran en la pestaña Temas SST)(*)',
        'Dereogada(*)',
        'Desripcion del artículo(*)',
        'Intereses del artículo (Si son varios separarlos por coma)(Los posibles valores se encuentran en la pestaña Intereses)(*)',
        'Artículo derogado (SI, NO)'
      ];

      return $columns;

    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:AP1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'name' => 'Arial',
                'bold' => true,
            ]
          ]
      );
    }

    /**
     * @return string
    */
    public function title(): string
    {
        return 'Leyes';
    }
}


/*'22', '¿Cuál es tu estado de animo hoy?', '2', '3', '1', NULL, '{"imgurl":"https://app.bigvision.com.co/images/thot.png","background_color":"light_blue","button":"Cuéntanos","optionstext":"¿Cuál es tu estado de animo hoy?","screen":1,"options":[{"text":"Contento"},{"text":"Tranquilo"},{"text":"Preocupado"},{"text":"Angustiado"},{"text":"Agotado"},{"text":"Aburrido"},{"text":"Esperanzado"},{"text":"Depresivo"},{"text":"Estresado"}]}', NULL, NULL


'23', '¿Alguna de las personas con las que convives se encuentran en cuarentena?', '2', '2', '1', NULL, '{\"imgurl\":\"https://app.bigvision.com.co/images/thot.png\",\"background_color\":\"light_blue\",\"button\":\"Cuéntanos\",\"optionstext\":\"¿Alguna de las personas con las que convives se encuentran en cuarentena?\",\"screen\":1,\"options\":[{\"text\":\"Sí\",\"jumpTo\":22},{\"text\":\"No\"},{\"text\":\"No estoy seguro (a)\"},{\"text\":\"Vivo con personal considerado(a) de alto riesgo por su trabajo: Ej. transporte, cajero, personal de salud\"}]}', NULL, NULL*/

