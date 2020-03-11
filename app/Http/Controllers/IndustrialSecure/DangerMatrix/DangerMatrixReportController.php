<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Jobs\IndustrialSecure\DangerMatrix\DangerMatrixReportExportJob;
use App\Traits\DangerMatrixTrait;
use App\Traits\Filtertrait;

class DangerMatrixReportController extends Controller
{
    use DangerMatrixTrait;
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:dangerMatrix_r|dangerMatrix_view_report, {$this->team}");
        $this->middleware("permission:dangerMatrix_export_report, {$this->team}", ['only' => 'reportExport']);
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $data = [];

        $url = "/industrialsecure/dangermatrix/report";
        $init = true;
        $filters = [];

        $conf = QualificationCompany::select('qualification_id')->first();

        if ($conf && $conf->qualification)
            $conf = $conf->qualification->name;
        else
            $conf = $this->getDefaultCalificationDm();

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        if ($conf)
        {
            $matriz_calification = $this->getMatrixCalification($conf);
            $data = $matriz_calification;

            /** FIltros */
            $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);
            
            $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
            
            $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
            
            $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
            
            $macroprocesses = !$init ? $this->getValuesForMultiselect($request->macroprocesses) : (isset($filters['macroprocesses']) ? $this->getValuesForMultiselect($filters['macroprocesses']) : []);
            
            $dangers = !$init ? $this->getValuesForMultiselect($request->dangers) : (isset($filters['dangers']) ? $this->getValuesForMultiselect($filters['dangers']) : []);
            
            $dangerDescription = !$init ? $this->getValuesForMultiselect($request->dangerDescription) : (isset($filters['dangerDescription']) ? $this->getValuesForMultiselect($filters['dangerDescription']) : []);
            //$matrix = $this->getValuesForMultiselect($request->matrix);
            $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
            /***********************************************/

            $dangersMatrix = DangerMatrix::select('sau_dangers_matrix.*')
                ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
                ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
                ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
                ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
                ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
                ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
                //->inMatrix($matrix, $filtersType['matrix'])
                ->get();

            foreach ($dangersMatrix as $keyMatrix => $itemMatrix)
            {
                foreach ($itemMatrix->activities as $keyActivity => $itemActivity)
                {
                    $activity_dangers = $itemActivity->dangers()->inDangers($dangers, $filtersType['dangers'])->inDangerDescription($dangerDescription, $filtersType['dangerDescription'])->get();

                    foreach ($activity_dangers as $keyDanger => $itemDanger)
                    {
                        $nri = -1;
                        $ndp = -1;

                        foreach ($itemDanger->qualifications as $keyQ => $itemQ)
                        {
                            if ($conf == 'Tipo 1')
                            {
                                if ($itemQ->typeQualification->description == 'NRI')
                                    $nri = $itemQ->value_id;

                                if ($itemQ->typeQualification->description == 'Nivel de Probabilidad')
                                    $ndp = $itemQ->value_id;
                            }
                        }

                        if ($conf == 'Tipo 1')
                            if (isset($data[$ndp]) && isset($data[$ndp][$nri]))
                                $data[$ndp][$nri]['count']++;
                    }
                }
            }

            $matriz = [];
            $headers = array_keys($data);
            $count = isset($data['Ha ocurrido en el sector hospitalario']) ? COUNT($data['Ha ocurrido en el sector hospitalario']) : 0;

            for ($i=0; $i < $count; $i++)
            { 
                $y = 0;

                foreach ($data as $key => $value)
                {
                    $x = 0;

                    foreach ($value as $key2 => $value2)
                    { 
                        $matriz[$x][$y] = array_merge($data[$key][$key2], ["row"=>$key, "col"=>$key2]);
                        $x++;
                    }

                    $y++;
                }
            }
        }
        
        return $this->respondHttp200([
            "data" => [
                "data" => $matriz,
                "headers" => $headers
            ]
        ]);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function reportDangerTable(Request $request)
    {
        $url = "/industrialsecure/dangermatrix/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        /** FIltros */
        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);
            
        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        
        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $macroprocesses = !$init ? $this->getValuesForMultiselect($request->macroprocesses) : (isset($filters['macroprocesses']) ? $this->getValuesForMultiselect($filters['macroprocesses']) : []);
        
        $dangers = !$init ? $this->getValuesForMultiselect($request->dangers) : (isset($filters['dangers']) ? $this->getValuesForMultiselect($filters['dangers']) : []);
        
        $dangerDescription = !$init ? $this->getValuesForMultiselect($request->dangerDescription) : (isset($filters['dangerDescription']) ? $this->getValuesForMultiselect($filters['dangerDescription']) : []);
        //$matrix = $this->getValuesForMultiselect($request->matrix);
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $dangers = DangerMatrix::select(
            'sau_dangers_matrix.id AS id',
            'sau_dm_dangers.name AS name',
            'sau_dm_activity_danger.danger_description AS danger_description',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area',
            'sau_employees_processes.types as types'
        )
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_qualification_danger', 'sau_dm_qualification_danger.activity_danger_id', 'sau_dm_activity_danger.id')
        ->join('sau_dm_qualification_types', 'sau_dm_qualification_types.id', 'sau_dm_qualification_danger.type_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_dangers_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_dangers_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_dangers_matrix.employee_area_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        //->inMatrix($matrix, $filtersType['matrix'])
        ->inDangers($dangers, $filtersType['dangers'])
        ->inDangerDescription($dangerDescription, $filtersType['dangerDescription'])
        ->where('sau_dm_activity_danger.qualification', $request->label)
        ->where('sau_dm_qualification_types.description', 'Nivel de Probabilidad')
        ->where('sau_dm_qualification_danger.value_id', $request->row);

        return Vuetable::of($dangers)
                    ->make();
    }

    public function reportExport(Request $request)
    {
        try
        {
            /** FIltros */
            $filters = [
                "regionals" => $this->getValuesForMultiselect($request->regionals),
                "headquarters" => $this->getValuesForMultiselect($request->headquarters),
                "areas" => $this->getValuesForMultiselect($request->areas),
                "processes" => $this->getValuesForMultiselect($request->processes),
                "macroprocesses" => $this->getValuesForMultiselect($request->macroprocesses),
                "dangers" => $this->getValuesForMultiselect($request->dangers),
                "dangerDescription" => $this->getValuesForMultiselect($request->dangerDescription),
                //"matrix" => $this->getValuesForMultiselect($request->matrix),
                "row" => $request->row,
                "col" => $request->col,
                "label" => $request->label,
                "filtersType" => $request->filtersType
            ];

            DangerMatrixReportExportJob::dispatch($this->user, $this->company, $filters);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}