<?php

namespace App\Managers;

use Exception;
use App\Managers\BaseManager;
use App\Traits\Filtertrait;
use App\Traits\RiskMatrixTrait;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use DB;
use Constant;

class RiskMatrixManager extends BaseManager
{
    use RiskMatrixTrait;
    use Filtertrait;
    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportInherent($request = [], $filters = [], $user)
    {
        $data = [];

        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
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

        $risksMatrix = RiskMatrix::select('sau_rm_risks_matrix.*')
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
            ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
            ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
            ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
            ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
            ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
            ->get();

        foreach ($risksMatrix as $keyMatrix => $itemMatrix)
        {
            foreach ($itemMatrix->subprocesses as $keySub => $itemSub)
            {
                $subprocess_risks = $itemSub->risks()->inRisks($risks, $filtersType['risks'])->get();

                foreach ($subprocess_risks as $keyRisk => $itemRisk)
                {
                    $frec = $itemRisk->description_inherent_frequency;

                    $imp = $itemRisk->description_inherent_impact;

                    if (isset($data[$frec]) && isset($data[$frec][$imp]))
                            $data[$frec][$imp]['count']++;
                }
            }
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

    public function reportRiskInherentTable($request = [], $filters = [], $user)
    {
        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
        $filters = [];

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
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risks = RiskMatrix::select(
            'sau_rm_risks_matrix.id AS id',
            'sau_rm_risk.name AS name',
            'sau_rm_risk.category AS category',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area',
            'sau_tags_processes.name as types',
            'sau_rm_subprocess_risk.description_inherent_frequency as frequency',
            'sau_rm_subprocess_risk.description_inherent_impact as impact'
        )
        ->join('sau_rm_risk_matrix_subprocess', 'sau_rm_risk_matrix_subprocess.risk_matrix_id', 'sau_rm_risks_matrix.id')
        ->join('sau_rm_subprocess_risk', 'sau_rm_subprocess_risk.rm_subprocess_id', 'sau_rm_risk_matrix_subprocess.id')
        ->join('sau_rm_risk', 'sau_rm_risk.id', 'sau_rm_subprocess_risk.risk_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->where('sau_rm_subprocess_risk.description_inherent_frequency',$request->rowI)
        ->where('sau_rm_subprocess_risk.description_inherent_impact',$request->colI);

        return $risks;
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportResidual($request = [], $filters = [], $user)
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

        $risksMatrix = RiskMatrix::select('sau_rm_risks_matrix.*')
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
            ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
            ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
            ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
            ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
            ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
            ->get();

        foreach ($risksMatrix as $keyMatrix => $itemMatrix)
        {
            foreach ($itemMatrix->subprocesses as $keySub => $itemSub)
            {
                $subprocess_risks = $itemSub->risks()->inRisks($risks, $filtersType['risks'])->get();

                foreach ($subprocess_risks as $keyRisk => $itemRisk)
                {
                    $frec = $itemRisk->description_residual_frequency;

                    $imp = $itemRisk->description_residual_impact;

                    if (isset($data[$frec]) && isset($data[$frec][$imp]))
                            $data[$frec][$imp]['count']++;
                }
            }
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

    public function reportRiskResidualTable($request = [], $filters = [], $user)
    {
        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
        $filters = [];

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

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risks = RiskMatrix::select(
            'sau_rm_risks_matrix.id AS id',
            'sau_rm_risk.name AS name',
            'sau_rm_risk.category AS category',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area',
            'sau_tags_processes.name as types',
            'sau_rm_subprocess_risk.description_residual_frequency as frequency',
            'sau_rm_subprocess_risk.description_residual_impact as impact'
        )
        ->join('sau_rm_risk_matrix_subprocess', 'sau_rm_risk_matrix_subprocess.risk_matrix_id', 'sau_rm_risks_matrix.id')
        ->join('sau_rm_subprocess_risk', 'sau_rm_subprocess_risk.rm_subprocess_id', 'sau_rm_risk_matrix_subprocess.id')
        ->join('sau_rm_risk', 'sau_rm_risk.id', 'sau_rm_subprocess_risk.risk_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->where('sau_rm_subprocess_risk.description_residual_frequency',$request->rowR)
        ->where('sau_rm_subprocess_risk.description_residual_impact',$request->colR);

        return $risks;
    }

    public function reportTableResidual($request = [], $filters = [], $user)
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

        $risksMatrix = RiskMatrix::select(
            'sau_rm_risks_matrix.*',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area'
        )
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->get();

        $table_report = [];

        foreach ($risksMatrix as $keyMatrix => $itemMatrix)
        {

            foreach ($itemMatrix->subprocesses as $keySub => $itemSub)
            {

                $subprocess_risks = $itemSub->risks()->inRisks($risks, $filtersType['risks'])->get();

                foreach ($subprocess_risks as $keyRisk => $itemRisk)
                {
                    $array_table = [];

                    $array_table['process'] = $itemMatrix->process;
                    $array_table['area'] = $itemMatrix->area;
                    
                    $frec = $itemRisk->description_residual_frequency;
                    $imp = $itemRisk->description_residual_impact;

                    $array_table['risk'] = ['sequence' => $itemRisk->risk_sequence, 'color' => $data[$frec][$imp]['color']];

                    $array_table['risk_name'] = $itemRisk->risk->name;

                    array_push($table_report, $array_table);
                }
            }
        }

        $data = [
            "data" => $table_report
        ];
        
        return $data;
    }

    public function reportRiskInherentTablePdf($request = [], $filters = [], $user)
    {
        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
        $filters = [];

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
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risks = RiskMatrix::select(
            'sau_rm_risks_matrix.id AS id',
            'sau_rm_risk.name AS name',
            'sau_rm_risk.category AS category',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area',
            'sau_tags_processes.name as types',
            'sau_rm_subprocess_risk.description_inherent_frequency as frequency',
            'sau_rm_subprocess_risk.description_inherent_impact as impact'
        )
        ->join('sau_rm_risk_matrix_subprocess', 'sau_rm_risk_matrix_subprocess.risk_matrix_id', 'sau_rm_risks_matrix.id')
        ->join('sau_rm_subprocess_risk', 'sau_rm_subprocess_risk.rm_subprocess_id', 'sau_rm_risk_matrix_subprocess.id')
        ->join('sau_rm_risk', 'sau_rm_risk.id', 'sau_rm_subprocess_risk.risk_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->where('sau_rm_subprocess_risk.description_inherent_frequency',$request->filtersTable['rowI'])
        ->where('sau_rm_subprocess_risk.description_inherent_impact',$request->filtersTable['colI']);

        return $risks;
    }

    public function reportRiskResidualTablePdf($request = [], $filters = [], $user)
    {
        $url = "/industrialsecure/riskmatrix/report";
        $init = true;
        $filters = [];

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

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);
        /***********************************************/

        $risks = RiskMatrix::select(
            'sau_rm_risks_matrix.id AS id',
            'sau_rm_risk.name AS name',
            'sau_rm_risk.category AS category',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area',
            'sau_tags_processes.name as types',
            'sau_rm_subprocess_risk.description_residual_frequency as frequency',
            'sau_rm_subprocess_risk.description_residual_impact as impact'
        )
        ->join('sau_rm_risk_matrix_subprocess', 'sau_rm_risk_matrix_subprocess.risk_matrix_id', 'sau_rm_risks_matrix.id')
        ->join('sau_rm_subprocess_risk', 'sau_rm_subprocess_risk.rm_subprocess_id', 'sau_rm_risk_matrix_subprocess.id')
        ->join('sau_rm_risk', 'sau_rm_risk.id', 'sau_rm_subprocess_risk.risk_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filtersType['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filtersType['headquarters'] : 'IN')
        ->inAreas($areas, isset($filtersType['areas']) ? $filtersType['areas'] : 'IN')
        ->inProcesses($processes, isset($filtersType['processes']) ? $filtersType['processes'] : 'IN')
        ->inMacroprocesses($macroprocesses, isset($filtersType['macroprocesses']) ? $filtersType['macroprocesses'] : 'IN')
        ->where('sau_rm_subprocess_risk.description_residual_frequency',$request->filtersTable['rowR'])
        ->where('sau_rm_subprocess_risk.description_residual_impact',$request->filtersTable['colR']);

        return $risks;
    }
}