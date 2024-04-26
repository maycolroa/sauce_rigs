<?php

namespace App\Exports\LegalAspects\Contracts\Contracts;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesTemplate;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

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
        $sheets = [];
        
        $sheets[] = new ContractsEmployeesTemplate($this->data, $this->company_id, $this->contract_id);        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
