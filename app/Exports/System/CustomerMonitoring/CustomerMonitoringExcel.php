<?php

namespace App\Exports\System\CustomerMonitoring;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\System\CustomerMonitoring\ReinstatementsTemplateExcel;
use App\Exports\System\CustomerMonitoring\DangerMatrixTemplateExcel;
use App\Exports\System\CustomerMonitoring\DangerousConditionsTemplateExcel;
use App\Exports\System\CustomerMonitoring\AbsenteeismTemplateExcel;
use App\Exports\System\CustomerMonitoring\ContractTemplateExcel;
use App\Exports\System\CustomerMonitoring\LegalMatrixTemplateExcel;

class CustomerMonitoringExcel implements WithMultipleSheets
{
    use Exportable;
    
    public function __construct()
    {
        //
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ReinstatementsTemplateExcel();
        $sheets[] = new DangerMatrixTemplateExcel();
        $sheets[] = new DangerousConditionsTemplateExcel();
        $sheets[] = new AbsenteeismTemplateExcel();
        $sheets[] = new ContractTemplateExcel();
        $sheets[] = new LegalMatrixTemplateExcel();

        return $sheets;
    }
}
