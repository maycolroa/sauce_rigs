<?php

namespace App\Exports\IndustrialSecure\Epp;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\Epp\ElementNotIdentImportTemplateExcel;
use App\Exports\Administrative\Positions\ElementTemplate;
use App\Exports\IndustrialSecure\Epp\LocationsTemplate;

class ElementNotIdentExcel implements WithMultipleSheets
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

        $sheets[] = new ElementNotIdentImportTemplateExcel(collect([]), $this->company_id);
        $sheets[] = new ElementTemplate($this->company_id);
        $sheets[] = new LocationsTemplate($this->company_id);
        return $sheets;
    }
}
    