<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Reports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\DangerousConditions\Reports\ReportListExcel;
use App\Exports\IndustrialSecure\DangerousConditions\Reports\ActivitiesExcel;
use App\Traits\ConfigurableFormTrait;

class ReportExcel implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $filters;
    
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

        $sheets[] = new ReportListExcel($this->company_id, $this->filters);
        $sheets[] = new ActivitiesExcel($this->company_id, $this->filters);

        return $sheets;
    }
}
