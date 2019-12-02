<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\InformManagerAudiometry;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\InformIndividualManagerAudiometry;

class AudiometryInformController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:biologicalMonitoring_audiometry_informs_r, {$this->team}", ['only' => 'data']);
        $this->middleware("permission:biologicalMonitoring_audiometry_inform_individual_r, {$this->team}", ['only' => 'dataIndividual']);
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
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $headquarters = $this->getValuesForMultiselect($request->headquarters);
        $areas = $this->getValuesForMultiselect($request->areas);
        $processes = $this->getValuesForMultiselect($request->processes);
        $deals = $this->getValuesForMultiselect($request->deals);
        $positions = $this->getValuesForMultiselect($request->positions);
        $years = $this->getValuesForMultiselect($request->years);
        $dates = [];
        $filtersType = $request->filtersType;

        if (isset($request->dateRange) && $request->dateRange)
        {
            $dates_request = explode('/', $request->dateRange);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Ymd'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Ymd'));
            }
            
        }
        
        $informManager = new InformManagerAudiometry($regionals, $headquarters, $areas, $processes, $deals, $positions, $years, $dates, $filtersType);
        
        return $this->respondHttp200($informManager->getInformData());
    }

    /**
     * Returns an array for a select type input
     *
     * @return Array
     */

    public function multiselectBar()
    {
        $keywords = $this->user->getKeywords();

        $select = [
            $keywords['regionals'] => "employee_regional_id", 
            $keywords['headquarters'] => "employee_headquarter_id",
            $keywords['processes'] => "employee_process_id",
            $keywords['areas'] => "employee_area_id",
            'Negocios' => 'deal',
            $keywords['positions'] => "employee_position_id",

        ];
    
        return $this->multiSelectFormat(collect($select));
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function dataIndividual(Request $request)
    {
        $employee_id = $request->employee_id;
        
        $informManager = new InformIndividualManagerAudiometry($employee_id);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}
