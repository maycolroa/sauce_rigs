<?php

namespace App\Exports\LegalAspects\LegalMatrix;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\LegalMatrix\LegalMatrixImportTemplate;
use App\Exports\LegalAspects\LegalMatrix\InterestTemplate;
use App\Exports\LegalAspects\LegalMatrix\LawTypeTemplate;
use App\Exports\LegalAspects\LegalMatrix\RiskAspectstTemplate;
use App\Exports\LegalAspects\LegalMatrix\SstRiskTemplate;

class LegalMatrixImport implements WithMultipleSheets
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

        $sheets[] = new LegalMatrixImportTemplate(collect([]), $this->company_id);
        $sheets[] = new InterestTemplate($this->company_id);
        $sheets[] = new LawTypeTemplate($this->company_id);
        $sheets[] = new RiskAspectstTemplate($this->company_id);
        $sheets[] = new SstRiskTemplate($this->company_id);

        return $sheets;
    }
}
