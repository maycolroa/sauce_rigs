<?php

namespace App\Exports\LegalAspects\Contracts\Contractor;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsImportLeyendTemplate;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsImportTemplateExcel;

class ContractsImportTemplate implements WithMultipleSheets
{
    use Exportable;
    
    protected $data;

    public function __construct()
    {
        $this->data = collect([]);

        $leyends = [
            'Los * significan campos obligatorios',
            'Campos pertenecientes al usuario de contacto del contratista',
            'Datos empresariales del contratista',
            'Datos adicionales del contratista'
        ];

        foreach ($leyends as $key => $value)
        {
            $this->data->push(['leyend'=>$value]);
        }
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ContractsImportTemplateExcel(collect([]));
        $sheets[] = new ContractsImportLeyendTemplate($this->data);

        return $sheets;
    }
}
