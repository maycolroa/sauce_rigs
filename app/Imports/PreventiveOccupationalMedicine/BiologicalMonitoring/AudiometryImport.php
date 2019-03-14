<?php

namespace App\Imports\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Administrative\Employee;
use App\Administrative\EmployeeEPS;
use App\Administrative\EmployeeArea;
use App\Administrative\EmployeeRegional;
use App\Administrative\EmployeePosition;
use App\Administrative\EmployeeBusiness;
use App\Administrative\EmployeeHeadquarter;
use App\Administrative\EmployeeProcess;
use App\Rules\AudiometryDate;
use App\Facades\Configuration;
use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Exception;
use App\Traits\AudiometryTrait;

class AudiometryImport implements ToCollection
{
    use AudiometryTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;

    public function __construct($company_id, $user)
    {
      $this->user = $user;
      $this->company_id = $company_id;
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {
            try
            {
                $employees_id = [];

                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabecera
                    {
                        if (COUNT($row) == 43)
                        {
                            $employee_id = $this->checkEmployee($row);

                            if ($employee_id)
                            {
                                array_push($employees_id, $employee_id);
                                $this->createAudiometry($row, $employee_id);
                            }
                        }
                        else
                        {
                            $this->setError('Formato inválido');
                            $this->setErrorData($row);
                        }
                    }
                }

                $employees_id = array_unique($employees_id);
                    
                foreach ($employees_id as $value)
                {
                    $this->calculateBaseAudiometry($value);
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de las audiometrias')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de audiometrias finalizo correctamente')
                        ->module('biologicalMonitoring/audiometry')
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/audiometrias_errors_'.date("YmdHis").'.xlsx';
                    Excel::store(new AudiometryImportErrorExcel(collect($this->errors_data), $this->errors), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de las audiometrias')
                        ->recipients($this->user)
                        ->message('El proceso de importación de las audiometrias finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('biologicalMonitoring/audiometry')
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                NotificationMail::
                    subject('Importación de las audiometrias')
                    ->recipients($this->user)
                    //->message('Se produjo un error durante el proceso de importación de las audiometrias. Contacte con el administrador')
                    ->message($e->getMessage())
                    ->module('biologicalMonitoring/audiometry')
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkEmployee($row)
    {
        $sql = Employee::where('identification', $row[0]);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();
        \Log::info($employee);
        \Log::info($row[0]);
        \Log::info($sql->toSql());
        if ($employee)
        {
            return $employee->id;
        }
        else
        {
            $eps = EmployeeEPS::where('code', $row[11])->first();

            if ($eps)
            {
                $fecha_nacimiento = $this->validateDate($row[4]);
                $fecha_ingreso = $this->validateDate($row[12]);

                $validator = Validator::make(
                    [
                        'identificacion'    => $row[0],
                        'nombre'            => $row[1],
                        'sexo'              => $row[2],
                        'email'             => $row[3],
                        'fecha_nacimiento'  => $fecha_nacimiento,
                        'cargo'             => $row[5],
                        'centro_costo'      => $row[6],
                        'regional'          => $row[7],
                        'sede'              => $row[8],
                        'proceso'           => $row[9],
                        'area'              => $row[10],
                        'fecha_ingreso'     => $fecha_ingreso
                    ],
                    [
                        'identificacion'   => 'required|numeric',
                        'nombre'           => 'required|string',
                        'sexo'             => 'required|string|in:M,m,F,f',
                        'email'            => 'required|email|unique:sau_employees,email,null,id,company_id,'.$this->company_id,
                        'fecha_nacimiento' => 'nullable|date',
                        'cargo'            => 'required',
                        'centro_costo'     => 'required',
                        'regional'         => 'required',
                        'sede'             => 'required',
                        'area'             => 'required',
                        'proceso'          => 'required',
                        'fecha_ingreso'    => 'required|date',
                    ]);

                if ($validator->fails())
                {
                    foreach ($validator->messages()->all() as $value)
                    {
                        $this->setError($value);
                    }
                }
                else 
                {
                    $regional_id = $this->checkRegional($row[7]);
                    $headquarter_id = $this->checkHeadquarter($regional_id, $row[8]);
                    $process_id = $this->checkProcess($headquarter_id, $row[9]);
                    $area_id = $this->checkArea($headquarter_id, $process_id, $row[10]);

                    $employee = Employee::create([
                        'identification' => $row[0],
                        'name' => $row[1],
                        'sex' => $row[2],
                        'email' => $row[3],
                        'date_of_birth' => $fecha_nacimiento,
                        'employee_position_id' => $this->checkPosition($row[5]),
                        'employee_business_id' => $this->checkBusiness($row[6]),
                        'employee_regional_id' => $regional_id,
                        'employee_headquarter_id' => $headquarter_id,
                        'employee_area_id' => $area_id,
                        'employee_process_id' => $process_id,
                        'employee_eps_id' => $eps->id,
                        'income_date' => $fecha_ingreso,
                        'company_id' => $this->company_id,
                    ]);

                    return $employee->id;
                }
            }
            else
            {
                $this->setError('Codigo EPS inválido');
            }
        }

        $this->setErrorData($row);
        return null;
    }

    private function checkArea($headquarter_id, $process_id, $area_name)
    {        
        $area = EmployeeRegional::select("sau_employees_areas.id as id")
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
            ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
            ->join('sau_process_area', 'sau_process_area.employee_process_id', 'sau_employees_processes.id')
            ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_process_area.employee_area_id')
            ->where('sau_employees_areas.name', $area_name)
            ->groupBy('sau_employees_areas.id', 'sau_employees_areas.name');

        $area->company_scope = $this->company_id;
        $area = $area->first();

        if (!$area)
        {
            $area = new EmployeeArea();
            $area->name = $area_name;
            $area->save();
        }
        else
            $area = EmployeeArea::find($area->id);
        
        $area->processes()->wherePivot('employee_headquarter_id','=',$headquarter_id)->detach($process_id);
        $area->processes()->attach($process_id, ['employee_headquarter_id' => $headquarter_id]);

        return $area->id;
    }

    private function checkRegional($name)
    {
        $regional = EmployeeRegional::query();
        $regional->company_scope = $this->company_id;
        $regional = $regional->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $regional->id;
    }

    private function checkPosition($name)
    {
        $position = EmployeePosition::query();
        $position->company_scope = $this->company_id;
        $position = $position->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $position->id;
    }

    private function checkBusiness($name)
    {
        $business = EmployeeBusiness::query();
        $business->company_scope = $this->company_id;
        $business = $business->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $business->id;
    }

    private function checkHeadquarter($regional_id, $headquarter)
    {
        $headquarter = EmployeeHeadquarter::firstOrCreate(['name' => $headquarter, 'employee_regional_id' => $regional_id], 
                                            ['name' => $headquarter, 'employee_regional_id' => $regional_id]);

        return $headquarter->id;
    }

    private function checkProcess($headquarter_id, $process_name)
    {
        $process = EmployeeRegional::select("sau_employees_processes.id as id")
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
            ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
            ->where('sau_employees_processes.name', $process_name)
            ->groupBy('sau_employees_processes.id', 'sau_employees_processes.name');

        $process->company_scope = $this->company_id;
        $process = $process->first();

        if (!$process)
        {
            $process = new EmployeeProcess();
            $process->name = $process_name;
            $process->save();
        }
        else
            $process = EmployeeProcess::find($process->id);
        
        $process->headquarters()->detach($headquarter_id);
        $process->headquarters()->attach($headquarter_id);

        return $process->id;
    }

    private function setError($message)
    {
        $this->errors[$this->key_row][] = ucfirst($message);
    }

    private function setErrorData($row)
    {
        $this->errors_data[] = $row;
        $this->key_row++;
    }

    private function validateDate($date)
    {
        try
        {
            $d = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        }
        catch (\Exception $e) {
            return $date;
        }

        return $d ? $d : null;
    }

    private function createAudiometry($row, $employee_id)
    {
        $fecha = $this->validateDate($row[13]);

        $NUMBERS_AVAILABLE_RESULTS = "0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,105,110,115,120";
        $EPP = implode(",", Configuration::getConfiguration('biologicalmonitoring_audiometries_select_epp'));
        $EXPOSITION_LEVEL = implode(",", Configuration::getConfiguration('biologicalmonitoring_audiometries_select_exposition_level'));

        $validator = Validator::make(
            [
                'fecha'                     => $fecha,
                'eventos_previos'           => $row[14],
                'empleado'                  => $employee_id,
                'epp'                       => array_map('trim', explode(",", $row[15])),
                'nivel_exposicion'          =>  $row[16],
                'aereo_derecha_500'         =>  $row[17],
                'aereo_derecha_1000'        =>  $row[18],
                'aereo_derecha_2000'        =>  $row[19],
                'aereo_derecha_3000'        =>  $row[20],
                'aereo_derecha_4000'        =>  $row[21],
                'aereo_derecha_6000'        =>  $row[22],
                'aereo_derecha_8000'        =>  $row[23],
                'aereo_izquierda_500'       =>  $row[24],
                'aereo_izquierda_1000'      =>  $row[25],
                'aereo_izquierda_2000'      =>  $row[26],
                'aereo_izquierda_3000'      =>  $row[27],
                'aereo_izquierda_4000'      =>  $row[28],
                'aereo_izquierda_6000'      =>  $row[29],
                'aereo_izquierda_8000'      =>  $row[30],
                'oseo_derecha_500'          =>  $row[31],
                'oseo_derecha_1000'         =>  $row[32],
                'oseo_derecha_2000'         =>  $row[33],
                'oseo_derecha_3000'         =>  $row[34],
                'oseo_derecha_4000'         =>  $row[35],
                'oseo_izquierda_500'        =>  $row[36],
                'oseo_izquierda_1000'       =>  $row[37],
                'oseo_izquierda_2000'       =>  $row[38],
                'oseo_izquierda_3000'       =>  $row[39],
                'oseo_izquierda_4000'       =>  $row[40],
                'recomendaciones_generales' =>  $row[41],
                'observaciones_generales'   =>  $row[42],
            ],
            [
                'fecha'                     => ['required','date','before_or_equal:today', new AudiometryDate(null, $employee_id, $fecha)],
                'eventos_previos'           => 'nullable',
                'empleado'                  => 'required|exists:sau_employees,id',
                "epp"                       => "nullable|array|min:1",
                "epp.*"                     => "nullable|in:$EPP",
                'nivel_exposicion'          => "nullable|in:$EXPOSITION_LEVEL",
                'aereo_izquierda_500'       => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_izquierda_1000'      => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_izquierda_2000'      => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_izquierda_3000'      => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_izquierda_4000'      => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_izquierda_6000'      => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_izquierda_8000'      => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_500'         => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_1000'        => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_2000'        => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_3000'        => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_4000'        => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_6000'        => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'aereo_derecha_8000'        => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_izquierda_500'        => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_izquierda_1000'       => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_izquierda_2000'       => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_izquierda_3000'       => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_izquierda_4000'       => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_derecha_500'          => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_derecha_1000'         => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_derecha_2000'         => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_derecha_3000'         => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'oseo_derecha_4000'         => "nullable|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
                'recomendaciones_generales' => 'nullable',
                'observaciones_generales'   => 'nullable',
            ],
            [
                'epp.*.in' => 'epp es inválido.',
            ]);

        if ($validator->fails())
        {
            foreach ($validator->messages()->all() as $value)
            {
                $this->setError($value);
            }
            
            $this->setErrorData($row);
        }
        else 
        {
            Audiometry::create([
                'date'               => $fecha,
                'previews_events'    => $row[14],
                'employee_id'        => $employee_id,
                'epp'                => $row[15],
                'exposition_level'   => $row[16],
                'air_right_500'      => $row[17],
                'air_right_1000'     => $row[18],
                'air_right_2000'     => $row[19],
                'air_right_3000'     => $row[20],
                'air_right_4000'     => $row[21],
                'air_right_6000'     => $row[22],
                'air_right_8000'     => $row[23],
                'air_left_500'       => $row[24],
                'air_left_1000'      => $row[25],
                'air_left_2000'      => $row[26],
                'air_left_3000'      => $row[27],
                'air_left_4000'      => $row[28],
                'air_left_6000'      => $row[29],
                'air_left_8000'      => $row[30],
                'osseous_right_500'  => $row[31],
                'osseous_right_1000' => $row[32],
                'osseous_right_2000' => $row[33],
                'osseous_right_3000' => $row[34],
                'osseous_right_4000' => $row[35],
                'osseous_left_500'   => $row[36],
                'osseous_left_1000'  => $row[37],
                'osseous_left_2000'  => $row[38],
                'osseous_left_3000'  => $row[39],
                'osseous_left_4000'  => $row[40],
                'recommendations'    => $row[41],
                'observation'        => $row[42],
            ]);
        }
    }
}