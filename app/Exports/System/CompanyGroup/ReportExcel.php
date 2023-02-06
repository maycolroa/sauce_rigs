<?php

namespace App\Exports\System\CompanyGroup;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\System\CompanyGroup\CompaniesTemplateExcel;
use App\Exports\System\CompanyGroup\LicensesTemplateExcel;
use App\Exports\System\CompanyGroup\LicensesUseTemplateExcel;

class ReportExcel implements WithMultipleSheets
{
    use Exportable;
    protected $group;
    
    public function __construct($group)
    {
        $this->group = $group;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new CompaniesTemplateExcel($this->group);
        $sheets[] = new LicensesTemplateExcel($this->group);
        $sheets[] = new LicensesUseTemplateExcel($this->group);
        

        return $sheets;
    }
}
