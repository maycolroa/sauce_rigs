<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\Filtertrait;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\InformManagerAudiometry;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\InformIndividualManagerAudiometry;

class AudiometryInformController extends Controller
{
    use Filtertrait;

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

        $url = "/preventiveoccupationalmedicine/biologicalmonitoring/audiometry/informs";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);
        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        $areas = !$init ?  $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        $processes = !$init ?  $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        $deals = !$init ?  $this->getValuesForMultiselect($request->deals) : (isset($filters['deals']) ? $this->getValuesForMultiselect($filters['deals']) : []);
        $positions = !$init ?  $this->getValuesForMultiselect($request->positions) : (isset($filters['positions']) ? $this->getValuesForMultiselect($filters['positions']) : []);
        $years = !$init ?  $this->getValuesForMultiselect($request->years) : (isset($filters['years']) ? $this->getValuesForMultiselect($filters['years']) : []);
        $dates = [];
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);

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

        $confLocation = $this->getLocationFormConfModule();
        $select = [];

        if ($confLocation['regional'] == 'SI')
            $select[$keywords['regionals']] = 'employee_regional_id';
        if ($confLocation['headquarter'] == 'SI')
            $select[$keywords['headquarters']] = 'employee_headquarter_id';
        if ($confLocation['process'] == 'SI')
            $select[$keywords['processes']] = 'employee_process_id';
        if ($confLocation['area'] == 'SI')
            $select[$keywords['areas']] = 'employee_area_id';

        $select['Negocios'] = 'deal';
        $select[$keywords['positions']] = "employee_position_id";
    
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
