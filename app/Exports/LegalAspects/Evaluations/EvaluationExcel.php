<?php

namespace App\Exports\LegalAspects\Evaluations;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Evaluations\EvaluationsListExcel;
use App\Exports\LegalAspects\Evaluations\EvaluationsExecutesExcel;

class EvaluationExcel implements WithMultipleSheets
{
    use Exportable;

    protected $company_id;
    protected $filters;
    
    public function __construct($company_id, $filters)
    {
        $this->company_id = $company_id;
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new EvaluationsListExcel($this->company_id, $this->filters);
        $sheets[] = new EvaluationsExecutesExcel($this->company_id, $this->filters);

        return $sheets;
    }
}
