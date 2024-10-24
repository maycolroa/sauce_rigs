<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Jobs\IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryExportJob;
use Session;
use App\Models\IndustrialSecure\DangerMatrix\QualificationHistory;
use App\Models\IndustrialSecure\DangerMatrix\ReportHistory;
use App\Traits\Filtertrait;
use App\Vuetable\Facades\Vuetable;

class DangerMatrixReportHistoryController extends Controller
{
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
        $url = "/industrialsecure/dangermatrix/report/history";
        $init = true;
        $filters = [];
        $showLabelCol = false;

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        /** FIltros */
        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);
            
        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);

        \Log::info($areas);
        
        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $macroprocesses = !$init ? $this->getValuesForMultiselect($request->macroprocesses) : (isset($filters['macroprocesses']) ? $this->getValuesForMultiselect($filters['macroprocesses']) : []);
        
        $dangers = !$init ? $this->getValuesForMultiselect($request->dangers) : (isset($filters['dangers']) ? $this->getValuesForMultiselect($filters['dangers']) : []);
        
        $dangerDescription = !$init ? $this->getValuesForMultiselect($request->dangerDescription) : (isset($filters['dangerDescription']) ? $this->getValuesForMultiselect($filters['dangerDescription']) : []);
        //$matrix = $this->getValuesForMultiselect($request->matrix);
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $dangersMatrix = ReportHistory::select('sau_dm_report_histories.*')
            ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
            ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
            ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
            ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
            ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
            ->inDangers($dangers, $filtersType['dangers'])
            ->inDangerDescription($dangerDescription, $filtersType['dangerDescription'])
            ->where("year", $request->year)
            ->where("month", $request->month)
            ->get();

        $conf = '';

        if ($dangersMatrix->count() > 0)
        {
            $conf = $dangersMatrix[0]->type_configuration;
        }

        $matriz_calification = QualificationHistory::
              where("year", $request->year)
            ->where("month", $request->month)
            ->where("type_configuration", $conf)
            ->where("company_id", $this->company)
            ->first();

        if ($matriz_calification)
            $matriz_calification = json_decode($matriz_calification->value, true);
        else
        {
            $matriz_calification = QualificationHistory::
                where("year", $request->year)
              ->where("month", $request->month)
              ->where("type_configuration", $conf)
              ->first();
              
              if ($matriz_calification)
                  $matriz_calification = json_decode($matriz_calification->value, true);
        }

        $data = $matriz_calification ? $matriz_calification : [];

        foreach ($dangersMatrix as $keyDanger => $itemDanger)
        {
            $nri = -1;
            $ndp = -1;
            $frec = -1;
            $sev = -1;

            $qualifications = json_decode($itemDanger->qualification, true);

            foreach ($qualifications as $keyQ => $itemQ)
            {
                if ($conf == 'Tipo 1')
                {
                    if ($itemQ["name"] == 'NRI')
                        $nri = $itemQ["value"];

                    if ($itemQ["name"] == 'Nivel de Probabilidad')
                        $ndp = $itemQ["value"];
                }
                else if ($conf == 'Tipo 2')
                {
                    if ($itemQ["name"] == 'Frecuencia')
                        $frec = $itemQ["value"];

                    if ($itemQ["name"] == 'Severidad')
                        $sev = $itemQ["value"];
                }
            }

            if ($conf == 'Tipo 1')
            {
                if (isset($data[$ndp]) && isset($data[$ndp][$nri]))
                    $data[$ndp][$nri]['count']++;
            }
            else if ($conf == 'Tipo 2')
            {
                if (isset($data[$sev]) && isset($data[$sev][$frec]))
                    $data[$sev][$frec]['count']++;
            }
        }

        $matriz = [];

        $headers = [];

        if ($conf == 'Tipo 1')
        {       
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
        else if ($conf == 'Tipo 2')
        {
            $showLabelCol = true;
            $headers = array_keys($data);
            $count = isset($data['MENOR']) ? COUNT($data['MENOR']) : 0;

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
                "headers" => $headers,
                "showLabelCol" => $showLabelCol
            ]
        ]);
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
                "filtersType" => $request->filtersType,
                "year" => $request->year,
                "month" => $request->month,
            ];

            DangerMatrixReportHistoryExportJob::dispatch($this->user, $this->company, $filters);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if ($request->has('column') && $request->get('column') != '')
        {
            if($request->has('keyword'))
            {
                $column = $request->column;

                $keyword = "%{$request->keyword}%";
                $data = ReportHistory::selectRaw("DISTINCT $column")
                ->where(function ($query) use ($keyword, $column) {
                    $query->orWhere($column, 'like', $keyword);
                })
                ->orderBy($column);

                if ($request->has('year'))
                    $data->where('year', $request->year);
                
                if ($request->has('month'))
                    $data->where('month', $request->month);

                $data = $data->take(30)->pluck($column, $column);

                if ($column == 'month')
                {
                    $new_data = [];

                    foreach ($data as $value)
                    {
                        $new_data[trans("months.$value")] = $value;
                    }

                    $data = collect($new_data);
                }

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($data)
                ]);
            }
            else
            {
                $column = $request->column;

                $keyword = "%{$request->keyword}%";
                $data = ReportHistory::selectRaw("DISTINCT $column")
                ->where($column, "<>", "")
                ->whereNotNull($column);

                if ($request->has('year'))
                    $data->where('year', $request->year);
                
                if ($request->has('month'))
                    $data->where('month', $request->month);

                $data = $data->pluck($column, $column);

                if ($column == 'month')
                {
                    $new_data = [];

                    foreach ($data as $value)
                    {
                        $new_data[trans("months.$value")] = $value;
                    }

                    $data = collect($new_data);
                }

                if ($request->has('tag') && $request->tag == "true")
                {
                    $data = array_unique(explode(",", implode(",", $data->toArray())));
                    $new_data = [];

                    foreach ($data as $value)
                    {
                        $new_data[$value] = $value;
                    }

                    $data = collect($new_data);
                }

                return $this->multiSelectFormat($data);
            }
        }

        return [];
    }

    public function reportDangerTable(Request $request)
    {
        $url = "/industrialsecure/dangermatrix/report/history";
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
        
        $dangers = !$init ? $this->getValuesForMultiselect($request->dangers) : (isset($filters['dangers']) ? $this->getValuesForMultiselect($filters['dangers']) : []);
        
        $dangerDescription = !$init ? $this->getValuesForMultiselect($request->dangerDescription) : (isset($filters['dangerDescription']) ? $this->getValuesForMultiselect($filters['dangerDescription']) : []);
        //$matrix = $this->getValuesForMultiselect($request->matrix);
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $dangers = ReportHistory::select('sau_dm_report_histories.*')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inDangers($dangers, $filtersType['dangers'])
        ->inDangerDescription($dangerDescription, $filtersType['dangerDescription'])
        ->where('qualification_text', $request->label)
        ->where('nivel_probabilily', $request->row)
        ->where("year", $request->year)
        ->where("month", $request->month);

        return Vuetable::of($dangers)
                    ->make();
    }

    public function reportDetail($id)
    {
        $danger = ReportHistory::find($id);

        $qualifications = json_decode($danger->qualification);
        
        $danger->nr_persons = $qualifications[1]->value;
        $danger->nr_economic = $qualifications[2]->value;
        $danger->nr_image = $qualifications[3]->value;
        $danger->nri = $qualifications[4]->value;

        $danger->locations = $this->prepareDataLocationForm($danger);

        return $this->respondHttp200([
            'data' => [
                'form' => $danger,
            ]
        ]);

    }

    protected function prepareDataLocationForm($model)
    {
        $data = [];

        $data['regional'] = $model->regional;
        $data['headquarter'] = $model->headquarter;
        $data['process'] = $model->process;
        $data['area'] = $model->area;

        /*if ($model->macroprocess_id)
        {            
            $data['macroprocess_id'] = $model->macroprocess_id;
            $data['multiselect_macroprocess'] = $model->macroprocess ? $model->macroprocess->multiselect() : null;
            $data['nomenclature'] = $model->nomenclature;
        }

        $data['multiselect_regional'] = $model->regional2 ? $model->regional2->multiselect() : null;
        $data['multiselect_headquarter'] = $model->headquarter2 ? $model->headquarter2->multiselect() : null;
        $data['multiselect_process'] = $model->process2 ? $model->process2->multiselect() : null;
        $data['multiselect_area'] = $model->area2 ? $model->area2->multiselect() : null;*/

        return $data;
    }
}