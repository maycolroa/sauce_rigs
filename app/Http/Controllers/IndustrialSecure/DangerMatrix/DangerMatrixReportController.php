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
        /*$regionals = $this->getValuesForMultiselect($request->regionals);
        $filtersType = $request->filtersType;*/

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
            $dangers = $this->getValuesForMultiselect($request->dangers);
            $matrix = $this->getValuesForMultiselect($request->matrix);
            $filtersType = $request->filtersType;
            /***********************************************/

            $dangersMatrix = DangerMatrix::select('*')
                ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
                ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
                ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
                ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
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
                            if (isset($data[$nri]) && isset($data[$nri][$ndp]))
                                $data[$nri][$ndp]['count']++;
                    }
                }
            }
        }
        
        return $this->respondHttp200($data);
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
        $dangers = $this->getValuesForMultiselect($request->dangers);
        $matrix = $this->getValuesForMultiselect($request->matrix);
        $filtersType = $request->filtersType;
        /***********************************************/

        $dangers = DangerMatrix::select(
            'sau_dangers_matrix.id AS id',
            'sau_dm_dangers.name AS name',
            'sau_dm_activity_danger.danger_description AS danger_description',
            'sau_dangers_matrix.name AS matrix'
        )
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_qualification_danger', 'sau_dm_qualification_danger.activity_danger_id', 'sau_dm_activity_danger.id')
        ->join('sau_dm_qualification_types', 'sau_dm_qualification_types.id', 'sau_dm_qualification_danger.type_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMatrix($matrix, $filtersType['matrix'])
        ->inDangers($dangers, $filtersType['dangers'])
        ->where('sau_dm_activity_danger.qualification', $request->label)
        ->where('sau_dm_qualification_types.description', 'NRI')
        ->where('sau_dm_qualification_danger.value_id', $request->row);

        return Vuetable::of($dangers)
                    ->make();
    }
}