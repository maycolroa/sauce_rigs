<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inform\LegalAspects\LegalMatrix\ReportManagerLaw;
use App\Jobs\LegalAspects\LegalMatrix\Reports\ReportLawExportJob;

class LawReportController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:laws_report_r, {$this->team}", ['only' => 'data']);
        $this->middleware("permission:laws_report_export, {$this->team}", ['only' => 'export']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $lawTypes = $this->getValuesForMultiselect($request->lawTypes);
        $riskAspects = $this->getValuesForMultiselect($request->riskAspects);
        $entities = $this->getValuesForMultiselect($request->entities);
        $sstRisks = $this->getValuesForMultiselect($request->sstRisks);
        $systemApply = $this->getValuesForMultiselect($request->systemApply);
        $lawNumbers = $this->getValuesForMultiselect($request->lawNumbers);
        $lawYears = $this->getValuesForMultiselect($request->lawYears);
        $repealed = $this->getValuesForMultiselect($request->repealed);
        $responsibles = $this->getValuesForMultiselect($request->responsibles);
        $interests = $this->getValuesForMultiselect($request->interests);
        $states = $this->getValuesForMultiselect($request->states);
        $filtersType = $request->filtersType;
        $category = $request->legalMatrixSelected;
        
        $reportManager = new ReportManagerLaw($lawTypes, $riskAspects, $entities, $sstRisks, $systemApply, $lawNumbers, $lawYears, $repealed, $responsibles, $interests, $states, $filtersType, $category);
        
        return $this->respondHttp200($reportManager->getInformData());
    }

    public function export(Request $request)
    {
        try
        {
            /** FIltros */
            $filters = [
                "lawTypes" => $this->getValuesForMultiselect($request->lawTypes),
                "riskAspects" => $this->getValuesForMultiselect($request->riskAspects),
                "entities" => $this->getValuesForMultiselect($request->entities),
                "sstRisks" => $this->getValuesForMultiselect($request->sstRisks),
                "systemApply" => $this->getValuesForMultiselect($request->systemApply),
                "lawNumbers" => $this->getValuesForMultiselect($request->lawNumbers),
                "lawYears" => $this->getValuesForMultiselect($request->lawYears),
                "repealed" => $this->getValuesForMultiselect($request->repealed),
                "responsibles" => $this->getValuesForMultiselect($request->responsibles),
                "interests" => $this->getValuesForMultiselect($request->interests),
                "states" => $this->getValuesForMultiselect($request->states),
                "filtersType" => $request->filtersType
            ];

            ReportLawExportJob::dispatch($this->user, $this->company, $filters);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function multiselectBar()
    {
        $select = [
            'Años' => "year",
            'Derogada' => "repealed",
            'Entidades' => "entity",
            'Intereses' => "interest",
            'Riesgos ambientales' => "riskAspects",
            'Riesgos SST' => "riskSst",
            'Sistema que aplica' => "systemApply", 
            'Tipo de norma' => "lawType",      
        ];
    
        return $this->multiSelectFormat(collect($select));
    }
}
