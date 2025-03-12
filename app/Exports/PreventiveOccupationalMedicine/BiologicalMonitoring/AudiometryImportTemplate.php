<?php

namespace App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportDataTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportEpsTemplateExcel;
use App\Exports\WarningImportTemplate;

class AudiometryImportTemplate implements WithMultipleSheets
{
    use Exportable;
    
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new AudiometryImportDataTemplateExcel(collect([]), $this->company_id);
        $sheets[] = new WarningImportTemplate();
        $sheets[] = new AudiometryImportEpsTemplateExcel($this->company_id);

        return $sheets;
    }
}
