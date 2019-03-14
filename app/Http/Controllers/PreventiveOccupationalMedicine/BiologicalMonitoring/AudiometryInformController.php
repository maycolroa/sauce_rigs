<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('auth');
        $this->middleware('permission:biologicalMonitoring_audiometry_r');
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
        $businesses = $this->getValuesForMultiselect($request->businesses);
        $positions = $this->getValuesForMultiselect($request->positions);
        $years = $this->getValuesForMultiselect($request->years);
        $dates = [];

        if (isset($request->dateRange) && $request->dateRange)
        {
            $dates_request = explode('/', $request->dateRange);

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Ymd'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Ymd'));
            }
            
        }
        
        $informManager = new InformManagerAudiometry($regionals, $headquarters, $areas, $processes, $businesses, $positions, $years, $dates);
        
        return $this->respondHttp200($informManager->getInformData());
    }

    /**
     * Returns an array for a select type input
     *
     * @return Array
     */

    public function multiselectBar()
    {
        $select = [
            'Regionales' => 'employee_regional_id',
            'Sedes' => 'employee_headquarter_id',
            'Macroprocesos' => 'employee_process_id',
            'Áreas' => 'employee_area_id',
            'Centro de Costos' => 'employee_business_id',
            'Cargos' => 'employee_position_id'
        ];
    
        return $this->multiSelectFormat(collect($select));
    }

    /**
     * Returns an array for a select type input
     *
     * @return Array
     */

    public function multiselectBarPercentage()
    {
        $select = [
            'Total' => 'total',
            '% respecto a la población' => 'percentage_x_employee',
            '% respecto a la categoría' => 'percentage_x_category'
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
