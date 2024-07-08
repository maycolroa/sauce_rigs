<?php

namespace App\Exports\LegalAspects\Contracts\Contracts;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesTemplate;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;
use App\Models\Administrative\Configurations\ConfigurationCompany;

class ContractsEmployeeImportErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors;
    protected $data;
    protected $company_id;
    
    public function __construct($data, $errors, $company_id, $contract_id)
    {
        $this->data = $data;
        $this->errors = $errors;
        $this->company_id = $company_id;
        $this->contract_id = $contract_id;
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
        
        $sheets[] = new ContractsEmployeesTemplate($this->data, $this->company_id, $this->contract_id, $configuration ? $configuration->value : 'NO');        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
