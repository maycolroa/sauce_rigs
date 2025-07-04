<?php

namespace App\Exports\LegalAspects\Contracts\Contracts;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\ActivityContractTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\ProyectContractTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\EpsTemplateExcel;
use App\Exports\LegalAspects\Contracts\Contracts\RhTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\WorkingDayTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\DepartamentMunicipalityTemplateExcel;
use App\Exports\Administrative\Employees\AfpTemplateExcel;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use App\Exports\WarningImportTemplate;

class ContractsEmployeesImport implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $contract;

    protected $data;
    protected $workingDay;
    
    public function __construct($company_id, $contract)
    {
        $this->data = collect([]);
        $this->workingDay = collect([]);

        $this->company_id = $company_id;
        $this->contract = $contract;

        $leyends = [
            'A', 'B', 'O','AB', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-','AB+', 'AB-'
        ];

        $working = [
            'Jornada Normal',
            'Jornada Nocturna',
            'Jornada Mixta',
            'Jornada Diurna'
        ];

        foreach ($leyends as $key => $value)
        {
            $this->data->push(['leyend'=>$value]);
        }

        foreach ($working as $key => $value)
        {
            $this->workingDay->push(['working'=>$value]);
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

        $sheets[] = new ContractsEmployeesTemplate(collect([]), $this->company_id, $this->contract, $configuration ? $configuration->value : 'NO');
        $sheets[] = new WarningImportTemplate();
        $sheets[] = new ActivityContractTemplate($this->contract,$this->company_id);
        $sheets[] = new AfpTemplateExcel($this->company_id);
        $sheets[] = new EpsTemplateExcel($this->company_id);
        $sheets[] = new WorkingDayTemplate($this->workingDay);
        $sheets[] = new DepartamentMunicipalityTemplateExcel($this->company_id);
        $sheets[] = new RhTemplate($this->data);

        if ($configuration && $configuration->value == 'SI')
            $sheets[] = new ProyectContractTemplate($this->contract,$this->company_id);

        return $sheets;
    }
}
