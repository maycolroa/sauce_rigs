<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportDataTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportEpsTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorListExcel;

class AudiometryImportErrorExcel implements WithMultipleSheets
{
    use Exportable;

    protected $errors;
    protected $data;
    
    public function __construct($data, $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new AudiometryImportDataTemplateExcel($this->data);
        $sheets[] = new AudiometryImportEpsTemplateExcel();
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);

        return $sheets;
    }
}
