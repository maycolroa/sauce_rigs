<?php

namespace App\Exports\PreventiveOccupationalMedicine\Reinstatements;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\CheckExcel;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\CheckVivaAirExcel;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\CheckEmpresarialExcel;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\MonitoringsExcel;
use App\Exports\PreventiveOccupationalMedicine\Reinstatements\TracingExcel;
use App\Traits\ConfigurableFormTrait;

class CheckExportExcel implements WithMultipleSheets
{
    use Exportable;
    use ConfigurableFormTrait;

    protected $company_id;
    protected $data;
    protected $keywords;
    
    public function __construct($company_id, $data)
    {
        $this->company_id = $company_id;
        $this->data = $data;
        $this->keywords = $this->getKeywordQueue($this->company_id);
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $formModel = $this->getFormModel('form_check', $this->company_id);
        
        $sheets = [];      
        
        if ($formModel == 'vivaAir')
        {
            $sheets[] = new CheckVivaAirExcel($this->company_id, $this->data['checks']);
            $sheets[] = new MonitoringsExcel($this->data['medicalMonitorings'], 'Seguimientos Medicos');
            $sheets[] = new MonitoringsExcel($this->data['laborMonitorings'], 'Seguimientos Laborales');
            $sheets[] = new TracingExcel($this->data['tracings'], $this->keywords['tracings']);
            $sheets[] = new TracingExcel($this->data['laborNotes'], $this->keywords['labor_notes']);
        }
        else if ($formModel == 'misionEmpresarial')
        {
            $sheets[] = new CheckEmpresarialExcel($this->company_id, $this->data['checks']);
            $sheets[] = new MonitoringsExcel($this->data['medicalMonitorings'], 'Seguimientos Medicos');
            $sheets[] = new TracingExcel($this->data['tracings'], $this->keywords['tracings']);
            $sheets[] = new TracingExcel($this->data['laborNotes'], $this->keywords['labor_notes']);
        }
        else
        {
            $sheets[] = new CheckExcel($this->company_id, $this->data['checks']);
            $sheets[] = new MonitoringsExcel($this->data['medicalMonitorings'], 'Seguimientos Medicos');
            $sheets[] = new MonitoringsExcel($this->data['laborMonitorings'], 'Seguimientos Laborales');
            $sheets[] = new TracingExcel($this->data['tracings'], $this->keywords['tracings']);
            $sheets[] = new TracingExcel($this->data['laborNotes'], $this->keywords['labor_notes']);
        }

        return $sheets;
    }
}
