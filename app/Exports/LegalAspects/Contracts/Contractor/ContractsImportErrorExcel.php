<?php

namespace App\Exports\LegalAspects\Contracts\Contractor;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contractor\ContrctsImportTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class ContractsImportErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors;
    protected $data;
    protected $company_id;
    
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
        
        $sheets[] = new ContrctsImportTemplateExcel($this->data, $this->company_id);        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
