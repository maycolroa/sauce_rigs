<?php

namespace App\Exports\System\Licenses;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Exports\System\Licenses\ReportGeneralExcel;
use App\Exports\System\Licenses\ReportModuleExcel;
use App\Exports\System\Licenses\ReportGroupExcel;
use App\Exports\System\Licenses\ReportGroupModuleExcel;
use DB;

class ReportExportExcel implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $reports = ConfigurationsCompany::company(1)->findByKey('license_reports_sends');

        if (!$reports)
            exit;

        $reports = explode(',', $reports);
                
        $sheets = [];      
        
        if (in_array('general', $reports))
        {
            $sheets[] = new ReportGeneralExcel($this->data['data']['general'], $this->data['headers']['general']);
        }

        if (in_array('module', $reports))
        {
            $sheets[] = new ReportModuleExcel($this->data['data']['module'], $this->data['headers']['module']);
        }

        if (in_array('group', $reports))
        {
            $sheets[] = new ReportGroupExcel($this->data['data']['group'], $this->data['headers']['group']);
        }

        if (in_array('group_module', $reports))
        {
            $sheets[] = new ReportGroupModuleExcel($this->data['data']['group_module'], $this->data['headers']['group_module']);
        }

        return $sheets;
    }
}
