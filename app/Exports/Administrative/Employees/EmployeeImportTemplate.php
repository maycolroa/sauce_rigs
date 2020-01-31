<?php

namespace App\Exports\Administrative\Employees;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Administrative\Employees\EmployeeImportDataTemplateExcel;
use App\Exports\Administrative\Employees\AfpTemplateExcel;
use App\Exports\Administrative\Employees\ArlTemplateExcel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportEpsTemplateExcel;
use App\Traits\ConfigurableFormTrait;

class EmployeeImportTemplate implements WithMultipleSheets
{
    use Exportable;
    use ConfigurableFormTrait;

    protected $company_id;
    protected $user;
    
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $formModel = $this->getFormModel('form_employee', $this->company_id);
        
        $sheets = [];

        $sheets[] = new EmployeeImportDataTemplateExcel(collect([]), $formModel, $this->company_id);
        $sheets[] = new AudiometryImportEpsTemplateExcel($this->company_id);

        if ($formModel == 'vivaAir' || $formModel == 'manpower')
        {
            $sheets[] = new AfpTemplateExcel($this->company_id);
        }
        else if ($formModel == 'misionEmpresarial')
        {
            $sheets[] = new AfpTemplateExcel($this->company_id);
            $sheets[] = new ArlTemplateExcel($this->company_id);
        }

        return $sheets;
    }
}
