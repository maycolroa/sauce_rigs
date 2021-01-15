<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionReportExcel;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionReportType2Excel;

class InspectionReport implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $filters;
    protected $user;
    
    public function __construct($company_id, $filters)
    {
        $this->company_id = $company_id;
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {        
        $sheets = [];

        $sheets[] = new InspectionReportExcel($this->company_id, $this->filters);
        $sheets[] = new InspectionReportType2Excel($this->company_id, $this->filters);

        return $sheets;
    }
}
