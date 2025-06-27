<?php

namespace App\Exports\PreventiveOccupationalMedicine\Absenteeism;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\Absenteeism\TableRecordImportTemplate;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class TableRecordImportErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors; 
    protected $data;
    protected $company_id;
    protected $table;
    
    public function __construct($data, $errors, $company_id, $table)
    {
        $this->data = $data;
        $this->errors = $errors;
        $this->company_id = $company_id;
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        $sheets[] = new TableRecordImportTemplate($this->table, $this->data, $this->company_id);        
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);
       
        return $sheets;
    }
}
