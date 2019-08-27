<?php

namespace App\Exports\PreventiveOccupationalMedicine\Reinstatements;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\MonitoringsExcel;
use App\Traits\ConfigurableFormTrait;

class CheckExportExcel implements WithMultipleSheets
{
    use Exportable;
    use ConfigurableFormTrait;

    protected $company_id;
    protected $data;
    
    public function __construct($company_id, $data)
    {
        $this->company_id = $company_id;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $formModel = $this->getFormModel('form_employee', $this->company_id);
        
        $sheets = [];

        //$sheets[] = new EmployeeImportDataTemplateExcel(collect([]), $formModel, $this->company_id);
        $sheets[] = new MonitoringsExcel($this->data['medicalMonitorings'], 'Seguimientos Medicos');

        if ($formModel == 'vivaAir')
        {
            
        }
        else if ($formModel == 'misionEmpresarial')
        {
            
        }

        return $sheets;
    }
}
