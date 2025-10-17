<?php

namespace App\Exports\IndustrialSecure\DangerMatrix;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixImportTemplateExcel;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixImportMassiveTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class DangerMatrixImportErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors;
    protected $data;
    protected $company_id;
    protected $massive;
    
    public function __construct($data, $errors, $company_id, $massive = false)
    {
        $this->data = $data;
        $this->errors = $errors;
        $this->company_id = $company_id;
        $this->massive = $massive;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        if ($this->massive)
            $sheets[] = new DangerMatrixImportMassiveTemplateExcel($this->data, $this->company_id);
        else
            $sheets[] = new DangerMatrixImportTemplateExcel($this->data, $this->company_id);        

        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
