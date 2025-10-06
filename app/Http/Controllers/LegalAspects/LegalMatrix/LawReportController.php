<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inform\LegalAspects\LegalMatrix\ReportManagerLaw;
use App\Jobs\LegalAspects\LegalMatrix\Reports\ReportLawExportJob;
use App\Models\LegalAspects\LegalMatrix\LawRiskOpportunity;
use App\Traits\Filtertrait;
use App\Vuetable\Facades\Vuetable;

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
        
        $riskOpportunity = !$init ? $this->getValuesForMultiselect($request->riskOpportunity) : (isset($filters['riskOpportunity']) ? $this->getValuesForMultiselect($filters['riskOpportunity']) : []);

        $states = !$init ? $this->getValuesForMultiselect($request->states) : (isset($filters['states']) ? $this->getValuesForMultiselect($filters['states']) : []);

        $range = $this->formatDatetimeToBetweenFilter($request->dateRange);

        $dates = $range ? $range : [];

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $category = $request->legalMatrixSelected;

         \Log::info($filtersType);
        
        $reportManager = new ReportManagerLaw($lawTypes, $riskAspects, $entities, $sstRisks, $systemApply, $lawNumbers, $lawYears, $repealed, $responsibles, $interests, $states, $filtersType, $category, $dates, $riskOpportunity);
        
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
                "riskOpportunity" => $this->getValuesForMultiselect($request->riskOpportunity),
                "states" => $this->getValuesForMultiselect($request->states),
                "dates" => $this->formatDatetimeToBetweenFilter($request->dateRange),
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

    public function reportRiskOpportunities(Request $request)
    {
        $data = LawRiskOpportunity::select(
            'sau_lm_laws.name AS law_name',
            'sau_lm_system_apply.name AS system', 
            'type_risk', 
            'risk_subsystem', 
            'risk_gestion',
            'sau_lm_law_risk_opportunity.description as opportunity',
            'sau_lm_law_risk_opportunity.description_no_apply as description_no_apply'
        )
        ->join('sau_lm_laws', 'sau_lm_laws.id', 'sau_lm_law_risk_opportunity.law_id')
        ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id');

        $filters = $request->filters;

        if (isset($filters['systemApply']) && count($filters['systemApply']) > 0)
            $data->whereIn('sau_lm_system_apply.id', $this->getValuesForMultiselect($filters["systemApply"]));
        
        if (isset($filters['typeLmRiskOpportunity']) && count($filters['typeLmRiskOpportunity']) > 0)
            $data->whereIn('sau_lm_law_risk_opportunity.type', $this->getValuesForMultiselect($filters["typeLmRiskOpportunity"]));
        
        if (isset($filters['typeRisk']) && count($filters['typeRisk']) > 0)
            $data->whereIn('sau_lm_law_risk_opportunity.type_risk', $this->getValuesForMultiselect($filters["typeRisk"]));
        
        if (isset($filters['subsystemRisk']) && count($filters['subsystemRisk']) > 0)
            $data->whereIn('sau_lm_law_risk_opportunity.risk_subsystem', $this->getValuesForMultiselect($filters["subsystemRisk"]));
        
        if (isset($filters['applyGestion']) && count($filters['applyGestion']) > 0)
            $data->whereIn('sau_lm_law_risk_opportunity.risk_gestion', $this->getValuesForMultiselect($filters["applyGestion"]));

        return Vuetable::of($data)->make();
    }
}
