<?php

namespace App\Inform\PreventiveOccupationalMedicine\BiologicalMonitoring;

use App\Models\Administrative\Employees\Employee;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Carbon\Carbon;

class InformIndividualManagerAudiometry
{
    /**
     * defines the availables informs
     *
     * IMPORTANT NOTE:
     * THERE MUST EXIST A METHOD THAT RETURNS THE INFORM DATA
     * WITH THE SAME EXACT NAME FOR EACH NAME WITHIN THIS ARRAY
     * 
     * @var array
     */
    const INFORMS = [
        'employee_information',
        'audiometries_right_ear',
        'audiometries_left_ear'
    ];

    /**
     * @var App\Administrative\Employee $employee
     */
    protected $employee;

    /**
     * create an instance and set the attribute class
     * @param Integer $employee_id
     */
    function __construct($employee_id)
    {
        $this->employee = Employee::findOrFail($employee_id);
    }

    /**
     * returns the data for the informs in the view according
     * to the $components parameter
     *
     * if $components is not defined, returns data for all the informs
     * 
     * @param  array $components
     * @return collection
     */
    public function getInformData($components = [])
    {
        if (!$components) {
            $components = $this::INFORMS;
        }
        $informData = collect([]);
        foreach ($components as $component) {
            $informData->put($component, $this->$component());
        }
        return $informData->toArray();
    }

    /**
     * Returns the employee information
     * @return collection
     */
    private function employee_information()
    {
        $data['identification'] =  $this->employee->identification;
        $data['name'] = $this->employee->name;
        $data['date_of_birth'] = $this->employee->date_of_birth ? (Carbon::createFromFormat('Y-m-d',$this->employee->date_of_birth))->format('M d Y') : '-';
        $data['age'] = $this->employee->date_of_birth ? Carbon::parse($this->employee->date_of_birth)->age : '-';
        $data['sex'] = $this->employee->sex;

        $data['email'] = $this->employee->email;
        $data['income_date'] = $this->employee->income_date ? (Carbon::createFromFormat('Y-m-d',$this->employee->income_date))->format('M d Y') : '-';;
        $data['regional'] = isset($this->employee->regional) ? $this->employee->regional->name : '-';
        $data['headquarter'] = isset($this->employee->headquarter) ? $this->employee->headquarter->name : '-';
        $data['area'] = isset($this->employee->area) ? $this->employee->area->name : '-';
        $data['process'] = isset($this->employee->process) ? $this->employee->process->name : '-';
        $data['position'] = isset($this->employee->position) ? $this->employee->position->name : '-';
        $data['business'] = isset($this->employee->business) ? $this->employee->business->name : '-';
        $data['eps'] = isset($this->employee->eps) ? ($this->employee->eps->code.' - '.$this->employee->eps->name) : '-';
        $data['deal'] = $this->employee->deal;

        return collect($data);
    }

    /**
     * returns the report of the right ear
     *
     * @return collection
     */
    public function audiometries_right_ear()
    {
        $orientation = "right";
        return $this->informLineEar($orientation);
    }

    /**
     * returns the report of the left ear
     *
     * @return collection
     */
    public function audiometries_left_ear()
    {
        $orientation = "left";
        return $this->informLineEar($orientation);
    }

    /**
     * calculates the ear report according to the orientation sent by parameter
     *
     * @param String $orientation
     * @return collection
     */
    private function informLineEar($orientation)
    {
        $column = "sau_bm_audiometries.air_".$orientation;

        $audiometries = Audiometry::selectRaw("
            sau_bm_audiometries.date as date,
            sau_bm_audiometries.base_type as base_type,
            ".$column."_500 as F500,
            ".$column."_1000 as F1000,
            ".$column."_2000 as F2000,
            ".$column."_3000 as F3000,
            ".$column."_4000 as F4000,
            ".$column."_6000 as F6000,
            ".$column."_8000 as F8000 
        ")
        ->where('sau_bm_audiometries.employee_id', '=', $this->employee->id)
        ->get();
        

        $data = [];
        $legend = [];

        foreach ($audiometries as $key => $value)
        {
            $legend_name = $value->base_type == 'Base' ? 'Base' : $value->date;

            array_push($legend, $legend_name);

            $arr = [
                ['name' => '500', 'value' => $value->F500],
                ['name' => '1000', 'value' => $value->F1000],
                ['name' => '2000', 'value' => $value->F2000],
                ['name' => '3000', 'value' => $value->F3000],
                ['name' => '4000', 'value' => $value->F4000],
                ['name' => '5000', 'value' => $value->F6000],
                ['name' => '8000', 'value' => $value->F8000]
            ];

            $info = [
                "name" => $legend_name,
                "type" => 'line',
                "data" => $arr,
                "label" => [
                    "show" => true,
                    "position" => "bottom"
                ]
            ];

            array_push($data, $info);
        }

        return collect([
            'xAxis' => ['500','1000','2000','3000','4000','6000','8000'],
            'legend' => $legend,
            'datasets' => [
                'data' => $data
            ]
        ]);
    }
}