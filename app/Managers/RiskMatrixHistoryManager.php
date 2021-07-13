<?php

namespace App\Managers;

use Exception;
use App\Managers\BaseManager;
use App\Traits\Filtertrait;
use App\Traits\RiskMatrixTrait;
use App\Models\IndustrialSecure\RiskMatrix\ReportHistory;
use DB;
use Constant;

class RiskMatrixHistoryManager extends BaseManager
{
    use RiskMatrixTrait;
    use Filtertrait;
    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportInherent($request = [], $filters = [], $user, $company)
    {
        $url = "/industrialsecure/dangermatrix/report/history";
        $init = true;
        $filters = [];
        $showLabelCol = false;

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($user, $url);

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
            ->where("company_id", $company)
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
        
        $data = [
            "data" => $matriz,
            "headers" => $headers,
            "showLabelCol" => $showLabelCol
        ];
        
        return $data;
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportResidual($request = [], $filters = [], $user, $company)
    {
        $data = [];

        $url = "/industrialsecure/dangermatrix/report/history";
        $init = true;
        $filters = [];
        $showLabelCol = false;

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($user, $url);

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
            ->where("company_id", $company)
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

        $data = [
            "data" => $matriz,
            "headers" => $headers,
            "showLabelCol" => $showLabelCol
        ];

        return $data;
    }

    public function reportTableResidual($request = [], $filters = [], $user, $company)
    {
        $data = [];

        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
        $filters = [];
        $showLabelCol = false;

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($user, $url);

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
        ->where("company_id", $company)
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

        $data = [
            "data" => $table_report
        ];
        
        return $data;
    }
}