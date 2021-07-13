<?php

namespace App\Exports\IndustrialSecure\RiskMatrix;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixReportResidualHistoryExcel;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixReportInherentHistoryExcel;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixTableReportResidualHistoryExcel;

class RiskMatrixReportHistory implements WithMultipleSheets
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

        $sheets[] = new RiskMatrixReportInherentHistoryExcel($this->company_id, $this->filters);
        $sheets[] = new RiskMatrixReportResidualHistoryExcel($this->company_id, $this->filters);
        $sheets[] = new RiskMatrixTableReportResidualHistoryExcel($this->company_id, $this->filters);

        return $sheets;
    }
}
