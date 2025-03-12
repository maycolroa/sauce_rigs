<?php

namespace App\Exports\LegalAspects\LegalMatrix\Laws;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\LegalMatrix\Laws\LegalMatrixImportTemplate;
use App\Exports\LegalAspects\LegalMatrix\Laws\InterestTemplate;
use App\Exports\LegalAspects\LegalMatrix\Laws\LawTypeTemplate;
use App\Exports\LegalAspects\LegalMatrix\Laws\RiskAspectstTemplate;
use App\Exports\LegalAspects\LegalMatrix\Laws\SstRiskTemplate;
use App\Exports\LegalAspects\LegalMatrix\Laws\EntityTemplate;
use App\Exports\WarningImportTemplate;

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
        $sheets[] = new WarningImportTemplate();
        $sheets[] = new InterestTemplate($this->company_id);
        $sheets[] = new LawTypeTemplate($this->company_id);
        $sheets[] = new RiskAspectstTemplate($this->company_id);
        $sheets[] = new SstRiskTemplate($this->company_id);
        $sheets[] = new EntityTemplate($this->company_id);

        return $sheets;
    }
}
