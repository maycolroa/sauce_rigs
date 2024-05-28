<?php

namespace App\Exports\LegalAspects\Contracts\Contractor;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsImportLeyendTemplate;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsImportTemplateExcel;
use App\Exports\LegalAspects\Contracts\Contractor\ActivityTemplate;
use App\Exports\LegalAspects\Contracts\Contractor\ProyectTemplate;
use App\Exports\LegalAspects\Contracts\Contractor\UsersExcel;
use App\Models\Administrative\Configurations\ConfigurationCompany;

class ContractsImportTemplate implements WithMultipleSheets
{
    use Exportable;
    
    protected $data;
    protected $company_id;

    public function __construct($company_id)
    {
        $this->data = collect([]);
        $this->company_id = $company_id;

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
        $configuration = ConfigurationCompany::select('value')->where('key', 'contracts_use_proyect');
        $configuration->company_scope = $this->company_id;
        $configuration = $configuration->first();

        $sheets = [];

        $sheets[] = new ContractsImportTemplateExcel(collect([]), $configuration ? $configuration->value : 'NO');
        $sheets[] = new ContractsImportLeyendTemplate($this->data);
        $sheets[] = new UsersExcel($this->company_id);
        $sheets[] = new ActivityTemplate($this->company_id);

        if ($configuration && $configuration->value == 'SI')
            $sheets[] = new ProyectTemplate($this->company_id);

        return $sheets;
    }
}
