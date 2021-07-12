<?php

namespace App\Exports\IndustrialSecure\RiskMatrix;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixReportResidualExcel;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixReportInherentExcel;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixTableReportResidualExcel;

class RiskMatrixReport implements WithMultipleSheets
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

        $sheets[] = new RiskMatrixReportInherentExcel($this->company_id, $this->filters);
        $sheets[] = new RiskMatrixReportResidualExcel($this->company_id, $this->filters);
        $sheets[] = new RiskMatrixTableReportResidualExcel($this->company_id, $this->filters);

        return $sheets;
    }
}
