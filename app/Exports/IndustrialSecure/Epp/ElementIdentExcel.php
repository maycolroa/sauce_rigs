<?php

namespace App\Exports\IndustrialSecure\Epp;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\Epp\ElementIdentImportTemplateExcel;
use App\Exports\IndustrialSecure\Epp\ElementTemplate;
use App\Exports\IndustrialSecure\Epp\LocationsTemplate;
use App\Exports\WarningImportTemplate;

class ElementIdentExcel implements WithMultipleSheets
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

        $sheets[] = new ElementIdentImportTemplateExcel(collect([]), $this->company_id);
        $sheets[] = new WarningImportTemplate();
        $sheets[] = new ElementTemplate($this->company_id, 1);
        $sheets[] = new LocationsTemplate($this->company_id);
        
        return $sheets;
    }
}
    