<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inform\LegalAspects\LegalMatrix\ReportManagerLaw;
use App\Jobs\LegalAspects\LegalMatrix\Reports\ReportLawExportJob;
use App\Traits\Filtertrait;

class LawReportController extends Controller
{
    use Filtertrait;

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
        $url = "/legalaspects/lm/laws/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $lawTypes = !$init ? $this->getValuesForMultiselect($request->lawTypes) : (isset($filters['lawTypes']) ? $this->getValuesForMultiselect($filters['lawTypes']) : []);

        $riskAspects = !$init ? $this->getValuesForMultiselect($request->riskAspects) : (isset($filters['riskAspects']) ? $this->getValuesForMultiselect($filters['riskAspects']) : []);

        $entities = !$init ? $this->getValuesForMultiselect($request->entities) : (isset($filters['entities']) ? $this->getValuesForMultiselect($filters['entities']) : []);

        $sstRisks = !$init ? $this->getValuesForMultiselect($request->sstRisks) : (isset($filters['sstRisks']) ? $this->getValuesForMultiselect($filters['sstRisks']) : []);

        $systemApply = !$init ? $this->getValuesForMultiselect($request->systemApply) : (isset($filters['systemApply']) ? $this->getValuesForMultiselect($filters['systemApply']) : []);

        $lawNumbers = !$init ? $this->getValuesForMultiselect($request->lawNumbers) : (isset($filters['lawNumbers']) ? $this->getValuesForMultiselect($filters['lawNumbers']) : []);

        $lawYears = !$init ? $this->getValuesForMultiselect($request->lawYears) : (isset($filters['lawYears']) ? $this->getValuesForMultiselect($filters['lawYears']) : []);

        $repealed = !$init ? $this->getValuesForMultiselect($request->repealed) : (isset($filters['repealed']) ? $this->getValuesForMultiselect($filters['repealed']) : []);

        $responsibles = !$init ? $this->getValuesForMultiselect($request->responsibles) : (isset($filters['responsibles']) ? $this->getValuesForMultiselect($filters['responsibles']) : []);

        $interests = !$init ? $this->getValuesForMultiselect($request->interests) : (isset($filters['interests']) ? $this->getValuesForMultiselect($filters['interests']) : []);

        $states = !$init ? $this->getValuesForMultiselect($request->states) : (isset($filters['states']) ? $this->getValuesForMultiselect($filters['states']) : []);
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

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
