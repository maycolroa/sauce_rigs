<?php

namespace App\Exports\LegalAspects\Contracts\Evaluations;

use App\Models\LegalAspects\Contracts\EvaluationContract;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationContractNotificationExcel;
use App\Exports\LegalAspects\Contracts\Evaluations\EvaluationContractObservationExcel;

class EvaluationContractExcel implements WithMultipleSheets
{
    use Exportable;

    protected $evaluation;
    protected $company_id;
    protected $id;
    
    public function __construct($company_id, $id)
    {
        $this->company_id = $company_id;
        $this->id = $id;

        $this->evaluation = EvaluationContract::
                select('sau_ct_evaluation_contract.*')
            ->where('sau_ct_evaluation_contract.id', $this->id);

        $this->evaluation->company_scope = $company_id;
        $this->evaluation = $this->evaluation->first();
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new EvaluationContractNotificationExcel($this->company_id, $this->id);

        if($this->evaluation->observation)
            $sheets[] = new EvaluationContractObservationExcel($this->evaluation->observation);

        return $sheets;
    }
}
