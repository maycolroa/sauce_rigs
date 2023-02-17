<?php

namespace App\Exports\Administrative\Employees;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Administrative\Employees\EmployeeImportDataTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportEpsTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class EmployeeImportInactiveErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors;
    protected $data;
    
    public function __construct($data, $errors, $company_id)
    {
        $this->data = $data;
        $this->errors = $errors;
        $this->company_id = $company_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
