<?php

namespace App\Exports\IndustrialSecure\DangerMatrix;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixExportMasiveExcel;

class DangerMatrixTemplateExportMasive implements WithMultipleSheets
{
    use Exportable;

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
        $sheets = [];

        foreach ($this->data['dangerMatrix'] as $key => $danger_matrix_id) 
        {            
            $sheets[] = new DangerMatrixExportMasiveExcel($this->company_id, $danger_matrix_id, $this->data['source'], $this->data['observations']);
        }

        return $sheets;
    }
}
