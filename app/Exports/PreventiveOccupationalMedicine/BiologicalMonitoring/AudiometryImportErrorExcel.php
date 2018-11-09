<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorDataExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorEpsExcel;
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

        $sheets[] = new AudiometryImportErrorDataExcel($this->data);
        $sheets[] = new AudiometryImportErrorEpsExcel();
        $sheets[] = new AudiometryImportErrorListExcel($this->errors);

        return $sheets;
    }
}
