<?php

namespace App\Exports\Administrative\Positions;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Administrative\Positions\PositionsTemplate;
use App\Exports\Administrative\Positions\ElementTemplate;
use App\Exports\WarningImportTemplate;

class PositionImportTemplateExcel implements WithMultipleSheets
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

        $sheets[] = new PositionsTemplate(collect([]), $this->company_id);
        $sheets[] = new WarningImportTemplate();
        $sheets[] = new ElementTemplate($this->company_id);
        return $sheets;
    }
}
