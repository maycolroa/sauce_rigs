<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Traits\DangerMatrixTrait;

class DangerMatrixReportController extends Controller
{
    use DangerMatrixTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dangerMatrix_r');
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

        $conf = QualificationCompany::select('qualification_id')->first();

        if ($conf && $conf->qualification)
            $conf = $conf->qualification->name;

        if ($conf)
        {
            $matriz_calification = $this->getMatrixCalification($conf);
            $data = $matriz_calification;

            /** FIltros */
            $regionals = $this->getValuesForMultiselect($request->regionals);
            $headquarters = $this->getValuesForMultiselect($request->headquarters);
            $areas = $this->getValuesForMultiselect($request->areas);
            $processes = $this->getValuesForMultiselect($request->processes);
            $macroprocesses = $this->getValuesForMultiselect($request->macroprocesses);
            $dangers = $this->getValuesForMultiselect($request->dangers);
            $matrix = $this->getValuesForMultiselect($request->matrix);
            $filtersType = $request->filtersType;
            /***********************************************/

            $dangersMatrix = DangerMatrix::select('sau_dangers_matrix.*')
                ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
                ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
                ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
                ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
                ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
                ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
                ->inMatrix($matrix, $filtersType['matrix'])
                ->get();

            foreach ($dangersMatrix as $keyMatrix => $itemMatrix)
            {
                foreach ($itemMatrix->activities as $keyActivity => $itemActivity)
                {
                    $activity_dangers = $itemActivity->dangers()->inDangers($dangers, $filtersType['dangers'])->get();

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
            $count = isset($data['Ha ocurrido en el sector Hospitalario']) ? COUNT($data['Ha ocurrido en el sector Hospitalario']) : 0;

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
        /** FIltros */
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $headquarters = $this->getValuesForMultiselect($request->headquarters);
        $areas = $this->getValuesForMultiselect($request->areas);
        $processes = $this->getValuesForMultiselect($request->processes);
        $macroprocesses = $this->getValuesForMultiselect($request->macroprocesses);
        $dangers = $this->getValuesForMultiselect($request->dangers);
        $matrix = $this->getValuesForMultiselect($request->matrix);
        $filtersType = $request->filtersType;
        /***********************************************/

        $dangers = DangerMatrix::select(
            'sau_dangers_matrix.id AS id',
            'sau_dm_dangers.name AS name',
            'sau_dm_activity_danger.danger_description AS danger_description',
            'sau_dangers_matrix.name AS matrix',
            'sau_employees_processes.types as types'
        )
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_qualification_danger', 'sau_dm_qualification_danger.activity_danger_id', 'sau_dm_activity_danger.id')
        ->join('sau_dm_qualification_types', 'sau_dm_qualification_types.id', 'sau_dm_qualification_danger.type_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->inMatrix($matrix, $filtersType['matrix'])
        ->inDangers($dangers, $filtersType['dangers'])
        ->where('sau_dm_activity_danger.qualification', $request->label)
        ->where('sau_dm_qualification_types.description', 'Nivel de Probabilidad')
        ->where('sau_dm_qualification_danger.value_id', $request->row);

        return Vuetable::of($dangers)
                    ->make();
    }
}