<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Jobs\IndustrialSecure\RiskMatrix\RisksMatrixReportHistoryExportJob;
use App\Traits\RiskMatrixTrait;
use Session;
use App\Models\IndustrialSecure\RiskMatrix\ReportHistory;
use App\Traits\Filtertrait;

class RiskMatrixReportHistoryController extends Controller
{
    use Filtertrait;
    use RiskMatrixTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:dangerMatrix_r|dangerMatrix_view_report, {$this->team}");
        $this->middleware("permission:dangerMatrix_export_report, {$this->team}", ['only' => 'reportExport']);*/
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportInherent(Request $request)
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
        
        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $macroprocesses = !$init ? $this->getValuesForMultiselect($request->macroprocesses) : (isset($filters['macroprocesses']) ? $this->getValuesForMultiselect($filters['macroprocesses']) : []);
        
        $risks = !$init ? $this->getValuesForMultiselect($request->risks) : (isset($filters['risks']) ? $this->getValuesForMultiselect($filters['risks']) : []);
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risksMatrix = ReportHistory::select('sau_rm_report_histories.*')
            ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
            ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
            ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
            ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
            ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
            ->inRisks($risks, $filtersType['risks'])
            ->where("company_id", $this->company)
            ->where("year", $request->year)
            ->where("month", $request->month)
            ->get();

        $matriz_calification = $this->getMatrixReport();

        $data = $matriz_calification ? $matriz_calification : [];

        foreach ($risksMatrix as $keyRisk => $itemRisk)
        {
            $frec = -1;
            $imp = -1;

            $qualifications = json_decode($itemRisk->qualification, true);

            foreach ($qualifications as $keyQ => $itemQ)
            {
                if ($itemQ["name"] == 'Frecuencia Inherente')
                    $frec = $itemQ["value"];

                if ($itemQ["name"] == 'Impacto Inherente')
                    $imp = $itemQ["value"];
            }

            if (isset($data[$frec]) && isset($data[$frec][$imp]))
                $data[$frec][$imp]['count']++;
        }

        $matriz = [];

        $showLabelCol = true;
        $headers = array_keys($data);
        $count = isset($data['Muy Bajo']) ? COUNT($data['Muy Bajo']) : 0;

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
        
        return $this->respondHttp200([
            "data" => [
                "data" => $matriz,
                "headers" => $headers,
                "showLabelCol" => $showLabelCol
            ]
        ]);
    }

    public function reportResidual(Request $request)
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
        
        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $macroprocesses = !$init ? $this->getValuesForMultiselect($request->macroprocesses) : (isset($filters['macroprocesses']) ? $this->getValuesForMultiselect($filters['macroprocesses']) : []);
        
        $risks = !$init ? $this->getValuesForMultiselect($request->risks) : (isset($filters['risks']) ? $this->getValuesForMultiselect($filters['risks']) : []);
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risksMatrix = ReportHistory::select('sau_rm_report_histories.*')
            ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
            ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
            ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
            ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
            ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
            ->inRisks($risks, $filtersType['risks'])
            ->where("year", $request->year)
            ->where("month", $request->month)
            ->get();

        $matriz_calification = $this->getMatrixReport();

        $data = $matriz_calification ? $matriz_calification : [];

        foreach ($risksMatrix as $keyRisk => $itemRisk)
        {
            $frec = -1;
            $imp = -1;

            $qualifications = json_decode($itemRisk->qualification, true);

            foreach ($qualifications as $keyQ => $itemQ)
            {
                if ($itemQ["name"] == 'Frecuencia Residual')
                    $frec = $itemQ["value"];

                if ($itemQ["name"] == 'Impacto Residual')
                    $imp = $itemQ["value"];
            }

            if (isset($data[$frec]) && isset($data[$frec][$imp]))
                $data[$frec][$imp]['count']++;
        }

        $matriz = [];

        $showLabelCol = true;
        $headers = array_keys($data);
        $count = isset($data['Muy Bajo']) ? COUNT($data['Muy Bajo']) : 0;

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
        
        return $this->respondHttp200([
            "data" => [
                "data" => $matriz,
                "headers" => $headers,
                "showLabelCol" => $showLabelCol
            ]
        ]);
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportTableResidual(Request $request)
    {
        $data = [];

        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
        $filters = [];
        $showLabelCol = false;

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

            $matriz_calification = $this->getMatrixReport();
            $data = $matriz_calification;

        /** FIltros */
        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);
        
        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
        
        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);
        
        $macroprocesses = !$init ? $this->getValuesForMultiselect($request->macroprocesses) : (isset($filters['macroprocesses']) ? $this->getValuesForMultiselect($filters['macroprocesses']) : []);
        
        $risks = !$init ? $this->getValuesForMultiselect($request->risks) : (isset($filters['risks']) ? $this->getValuesForMultiselect($filters['risks']) : []);

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risksMatrix = ReportHistory::select('sau_rm_report_histories.*')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->inRisks($risks, $filtersType['risks'])
        ->where("year", $request->year)
        ->where("month", $request->month)
        ->get();

        $table_report = [];

        foreach ($risksMatrix as $keyMatrix => $itemMatrix)
        {
            $frec = -1;
            $imp = -1;
            $array_table = [];

            $array_table['process'] = $itemMatrix->process;
            $array_table['area'] = $itemMatrix->area;

            $qualifications = json_decode($itemMatrix->qualification, true);
            
            foreach ($qualifications as $keyQ => $itemQ)
            {
                if ($itemQ["name"] == 'Frecuencia Residual')
                    $frec = $itemQ["value"];

                if ($itemQ["name"] == 'Impacto Residual')
                    $imp = $itemQ["value"];
            }

            $array_table['risk'] = ['sequence' => $itemMatrix->risk_sequence, 'color' => $data[$frec][$imp]['color']];

            $array_table['risk_name'] = $itemMatrix->risk;

            array_push($table_report, $array_table);
        }

        return $this->respondHttp200([
            "data" => [
                "data" => $table_report
            ]
        ]);
    }

    public function reportExportExcel(Request $request)
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
                "risks" => $this->getValuesForMultiselect($request->risks),
                "filtersType" => $request->filtersType,
                "year" => $request->year,
                "month" => $request->month,
            ];

            RisksMatrixReportHistoryExportJob::dispatch($this->user, $this->company, $filters);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function getDataExportPdf()
    {
        $data = [
            'inherent_report' => $this->inherent_report,
            'residual_report' => $this->residual_report,
            'inherent_report_table' => $this->inherent_report_table,
            'residual_report_table' => $this->residual_report_table,
            'table_report_residual' => $this->table_report_residual
        ];

        return $data;
    }

    public function reportExportPdf()
    {
        $report = $this->getDataExportPdf();

        \Log::info($report);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.qualificationListCheck', ['report' => $report] );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Reportes matriz de riesgos.pdf');
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

                return $this->multiSelectFormat($data);
            }
        }

        return [];
    }
}