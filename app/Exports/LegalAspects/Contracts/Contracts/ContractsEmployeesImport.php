<?php

namespace App\Exports\LegalAspects\Contracts\Contracts;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\ActivityContractTemplate;
use App\Exports\LegalAspects\Contracts\Contracts\EpsTemplateExcel;
use App\Exports\LegalAspects\Contracts\Contracts\RhTemplate;
use App\Exports\Administrative\Employees\AfpTemplateExcel;

class ContractsEmployeesImport implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $contract;

    protected $data;
    
    public function __construct($company_id, $contract)
    {
        $this->data = collect([]);

        $this->company_id = $company_id;
        $this->contract = $contract;

        $leyends = [
            'A', 'B', 'O','AB', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-','AB+', 'AB-'
        ];

        foreach ($leyends as $key => $value)
        {
            $this->data->push(['leyend'=>$value]);
        }
    }

    /**
     * @return array
     */
    public function sheets(): array
    {        
        $sheets = [];

        $sheets[] = new ContractsEmployeesTemplate(collect([]), $this->company_id, $this->contract);
        $sheets[] = new ActivityContractTemplate($this->contract,$this->company_id);
        $sheets[] = new AfpTemplateExcel($this->company_id);
        $sheets[] = new EpsTemplateExcel($this->company_id);
        $sheets[] = new RhTemplate($this->data);

        return $sheets;
    }
}
