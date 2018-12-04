<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring\InformManagerAudiometry;
use Session;

class AudiometryInformController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    /*function __construct()
    {
        $this->middleware('auth');
    }*/

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
        
        $informManager = new InformManagerAudiometry($regionals, $headquarters, $areas, $processes, $businesses, $positions, $years);
        
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
            'Áreas' => 'employee_area_id',
            'Procesos' => 'employee_process_id',
            'Centro de Costos' => 'employee_business_id',
            'Cargos' => 'employee_position_id'
        ];
    
        return $this->multiSelectFormat(collect($select));
    }
}
