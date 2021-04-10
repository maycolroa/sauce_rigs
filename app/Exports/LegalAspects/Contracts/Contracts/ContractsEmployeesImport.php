<?php

namespace App\Exports\LegalAspects\Contracts\Contracts;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesTemplate;use App\Exports\LegalAspects\Contracts\Contracts\ActivityContractTemplate;

class ContractsEmployeesImport implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $contract;
    
    public function __construct($company_id, $contract)
    {
        $this->company_id = $company_id;
        $this->contract = $contract;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {        
        $sheets = [];

        $sheets[] = new ContractsEmployeesTemplate(collect([]), $this->company_id, $this->contract);
        $sheets[] = new ActivityContractTemplate($this->contract,$this->company_id);

        return $sheets;
    }
}
