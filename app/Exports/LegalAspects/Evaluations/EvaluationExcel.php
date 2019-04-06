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
    
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new EvaluationsListExcel($this->company_id);
        $sheets[] = new EvaluationsExecutesExcel($this->company_id);

        return $sheets;
    }
}
