<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationsListExcel;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationsExecutesExcel;

class EvaluationExcel implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $filters;
    protected $evaluation_contract_id;
    
    public function __construct($company_id, $filters, $evaluation_contract_id = NULL)
    {
        $this->company_id = $company_id;
        $this->filters = $filters;
        $this->evaluation_contract_id = $evaluation_contract_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new EvaluationsListExcel($this->company_id, $this->filters, $this->evaluation_contract_id);
        $sheets[] = new EvaluationsExecutesExcel($this->company_id, $this->filters, $this->evaluation_contract_id);
        $sheets[] = new EvaluationsActivityExcel($this->company_id, $this->filters, $this->evaluation_contract_id);

        return $sheets;
    }
}
