<?php

namespace App\Exports\IndustrialSecure\DangerousConditions\Inspections;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionListExcel;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\QualificationsExcel;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\ActivitiesExcel;
use App\Traits\ConfigurableFormTrait;

class InspectionExcel implements WithMultipleSheets
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

        $sheets[] = new InspectionListExcel($this->company_id, $this->filters);
        $sheets[] = new QualificationsExcel($this->company_id, $this->filters);
        $sheets[] = new ActivitiesExcel($this->company_id, $this->filters);

        return $sheets;
    }
}
