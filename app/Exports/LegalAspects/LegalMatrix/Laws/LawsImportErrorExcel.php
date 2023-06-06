<?php

namespace App\Exports\LegalAspects\LegalMatrix\Laws;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\LegalMatrix\Laws\LegalMatrixImportTemplate;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class LawsImportErrorExcel implements WithMultipleSheets
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
        
        $sheets[] = new LegalMatrixImportTemplate($this->data, $this->company_id);        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
