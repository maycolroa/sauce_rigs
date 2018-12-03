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
        $regionals = collect($request->regionals)
        ->transform(function ($regional, $index) {
            return $regional['value'];
        });

        $headquarters = collect($request->headquarters)
        ->transform(function ($headquarter, $index) {
            return $headquarter['value'];
        });

        $areas = collect($request->areas)
        ->transform(function ($area, $index) {
            return $area['value'];
        });

        $processes = collect($request->processes)
        ->transform(function ($process, $index) {
            return $process['value'];
        });

        $businesses = collect($request->businesses)
        ->transform(function ($business, $index) {
            return $business['value'];
        });

        $positions = collect($request->positions)
        ->transform(function ($position, $index) {
            return $position['value'];
        });
        
        $years = collect($request->years)
        ->transform(function ($year, $index) {
            return $year['value'];
        });
        
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
