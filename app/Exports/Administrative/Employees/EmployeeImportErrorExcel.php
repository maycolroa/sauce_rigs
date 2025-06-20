<?php

namespace App\Exports\Administrative\Employees;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Administrative\Employees\EmployeeImportDataTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportEpsTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class EmployeeImportErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors;
    protected $data;
    protected $formModel;
    protected $company_id;
    
    public function __construct($data, $errors, $formModel, $company_id)
    {
        $this->data = $data;
        $this->errors = $errors;
        $this->formModel = $formModel;
        $this->company_id = $company_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        $sheets[] = new EmployeeImportDataTemplateExcel($this->data, $this->formModel, $this->company_id);
        $sheets[] = new AudiometryImportEpsTemplateExcel($this->company_id);

        if ($this->formModel == 'vivaAir')
        {
            $sheets[] = new AfpTemplateExcel($this->company_id);
        }
        else if ($this->formModel == 'misionEmpresarial')
        {
            $sheets[] = new AfpTemplateExcel($this->company_id);
            $sheets[] = new ArlTemplateExcel($this->company_id);
        }
        else if ($this->formModel == 'ingeomega')
        {
            $sheets[] = new AfpTemplateExcel($this->company_id);
        }
        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
