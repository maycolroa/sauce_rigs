<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportDataTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportEpsTemplateExcel;

class AudiometryImportTemplate implements WithMultipleSheets
{
    use Exportable;
    
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new AudiometryImportDataTemplateExcel(collect([]));
        $sheets[] = new AudiometryImportEpsTemplateExcel();

        return $sheets;
    }
}
