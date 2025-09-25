<?php

namespace App\Imports\LegalAspects;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Models\LegalAspects\Contracts\LogImportSocialSecure;
use App\Facades\Configuration;
use App\Exports\Administrative\Employees\EmployeeImportInactiveErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Validator;
use Exception;
use Illuminate\Support\Facades\Storage;

class ContractEmployeeImportSocialSecure implements ToCollection
{
    private $company_id;
    private $user;
    private $sheet = 1;
    private $key_row = 2;
    private $errors = [];
    private $errors_data = [];
    private $list = [];
    private $file_social_secure;
    private $holiday;

    public function __construct($company_id, $user, $contract, $file_social_secure, $description, $path_file_employee)
    {        
      $this->user = $user;
      $this->contract = $contract;
      $this->company_id = $company_id;
      $this->file_social_secure = $file_social_secure;
      $this->path_file_employee = $path_file_employee;
      $this->description = $description;

	  $this->calculateForYear();
    }

    public function logImport()
    {        
        $log_import = new LogImportSocialSecure;
        $log_import->company_id = $this->company_id;
        $log_import->user_id = $this->user->id;
        $log_import->contract_id = $this->contract->id;
        $log_import->description = $this->description;
        $log_import->path_file_employee = $this->path_file_employee;
        $log_import->path_file_social_secure = Storage::disk('s3')->url('legalAspects/files/'. $this->file_social_secure);
        $log_import->save();
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {            
            try
            {
                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabecera
                    {
                        if (isset($row[0]) && $row[0])
                            $this->checkEmployee($row);
                        else
                            $this->setErrorData($row);
                    }
                }


                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Carga de Seguridad Social de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de carga de Seguridad Social de todos los registros de empleados finalizo correctamente')
                        ->module('employees')
                        ->event('Job: ContractEmployeeImportSocialSecureJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    \Log::info('Error en la carga de Seguridad Social de empleados: '); 
                    \Log::info($this->errors); 
                    $nameExcel = 'export/1/empleados_errors_'.date("YmdHis").'.xlsx';                    
                    Excel::store(new EmployeeImportInactiveErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Carga de Seguridad Social de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de carga de la seguridad social de empleados finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('employees')
                        ->event('Job: ContractEmployeeImportSocialSecureJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info('Error en la carga de Seguridad Social de empleados: '); 
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Carga de Seguridad Social de empleados')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de carga de Seguridad Social de empleados. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('employees')
                    ->event('Job: ContractEmployeeImportSocialSecureJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkEmployee($row)
    {
        $data = [
            'identificacion' => $row[0],
        ];

        $sql = ContractEmployee::where('identification', $data['identificacion'])->where('contract_id', $this->contract->id);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();

        if (!$employee)
        {
            $this->setError('La identificación no pertenece a ningun empleado');
            $this->setErrorData($row);

            return null;
        }

        $rules = ['identificacion' => 'required|numeric'];

        $validator = Validator::make($data, $rules, 
        [
            'identificacion.required' => 'El campo Identificación es obligatorio.'
        ]);

        if ($validator->fails())
        {
            foreach ($validator->messages()->all() as $value)
            {
                $this->setError($value);
            }

            $this->setErrorData($row);
            return null;
        }
        else 
        {
            $fileUpload = new FileUpload();
            $fileUpload->user_id = $this->user->id;
            $fileUpload->name = 'Seguridad Social';
            $fileUpload->file = $this->file_social_secure;
            $fileUpload->expirationDate = NULL;
            $fileUpload->apply_file = 'SI';
            $fileUpload->apply_motive = NULL;
            $fileUpload->module = 'Empleados';
            $fileUpload->save();

            $fileUpload->contracts()->sync([$this->contract->id]);
            $class_document = [];

            foreach ($employee->activities as $key => $activity) 
            {          
              $class_document = array_merge($class_document, $activity->documents->where('class', 'Seguridad social')->pluck('id')->toArray());
            }

            $ids = [];

            foreach ($class_document as $value) 
            {               
                $ids[$value] = ['employee_id' => $employee->id];
            }

            $fileUpload->documents()->sync($ids);

            $state = new FileModuleState;
            $state->contract_id = $this->contract->id;
            $state->file_id = $fileUpload->id;
            $state->module = 'Empleados';
            $state->state = 'CREADO';                            
            $state->date = date('Y-m-d');
            $state->save();

            $day_expiration = Carbon::parse($this->calculateFirstDayOfMonth());

            $num_days = $this->contract->social_security_working_day;

            for ($i=0; $i < $num_days; $i++) 
            { 
                if ($i == $num_days - 1)
                    $day_expiration = $day_expiration;
                else
                    $day_expiration = $day_expiration->addDay(1);

                $day_week = $day_expiration->dayOfWeek;

                if ($day_week == 6)
                    $day_expiration = $day_expiration->addDay(2);
                else if ($day_week == 0)
                    $day_expiration = $day_expiration->addDay(1);

                $is_holiday = $this->isHoliday($day_expiration->format('Y-m-d'));

                if ($is_holiday)
                    $day_expiration = $day_expiration->addDay(1);
                
                \Log::info('foreach: '.$day_expiration->format('Y-m-d'));
            }

            \Log::info($day_expiration->format('Y-m-d'));

            $fileUpload->expirationDate = $day_expiration->format('Y-m-d');   
            $fileUpload->save();     

            $employee->update([
                'state' => 'Pendiente'
            ]);

            return true;
        }
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

    private function calculateFirstDayOfMonth()
    {
        $year = date("Y");
        $month = date("n")+1;

        $first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
        $first_day_of_week = date("w", $first_day_of_month);

        if ($first_day_of_week == 6)
            $first_business_day = mktime(0, 0, 0, $month, 3, $year); 
        else if ($first_day_of_week == 0) 
            $first_business_day = mktime(0, 0, 0, $month, 2, $year);
        else 
            $first_business_day = $first_day_of_month;

        $first_business_day_formatted = date("Y-m-d", $first_business_day);

        return $first_business_day_formatted;
    }

    public function calculateForYear($year = 0)
    {
		if ($year <= 1970 || $fecha>=2037) 
            $year = intval(date ( 'Y' ));
				
		// Fixed dates
		$this->list[] = $year."-01-01"; // Año nuevo
		$this->list[] = $year."-05-01"; // Dia del Trabajo 1 de Mayo
		$this->list[] = $year."-07-20"; // Independencia 20 de Julio
		$this->list[] = $year."-08-07"; // Batalla de Boyacá 7 de Agosto
		$this->list[] = $year."-12-08"; // Inmaculada 8 diciembre
		$this->list[] = $year."-12-25"; // Navidad 25 de diciembre		

		// These dates are moved to the next monday
		$this->list[] = $this->moveToMonday($year, 01, 06); // Reyes Magos Enero 6 (01-06)
		$this->list[] = $this->moveToMonday($year, 03, 19); // Día de san Jose Marzo 19 (03-19)
		$this->list[] = $this->moveToMonday($year, 06, 29); // San Pedro y San Pablo Junio 29 (06-29)
		$this->list[] = $this->moveToMonday($year, 8, 15); // Asunción Agosto 15 (08-15)
		$this->list[] = $this->moveToMonday($year, 10, 12); // Descubrimiento de América Oct 12 (10-12)
		$this->list[] = $this->moveToMonday($year, 11, 01); // Todos los santos Nov 1 (11-01)
		$this->list[] = $this->moveToMonday($year, 11, 11); // Independencia de Cartagena Nov 11 (11,11)
		$this->list[] = $this->moveToMonday($year, 9, 02); 

		// Holidays relative to the easterDate
		
		// Fixed
		$this->list[] = $this->calculateFromEasterDate($year, -03, false ); // jueves santo (3 días antes de pascua)
		$this->list[] = $this->calculateFromEasterDate($year, -02, false ); // viernes santo (2 días antes de pascua)
		
		// Moved to monday
		$this->list[] = $this->calculateFromEasterDate($year, 36, true ); // Ascensión del Señor (Sexto domingo después de Pascua) - 36 días
		$this->list[] = $this->calculateFromEasterDate($year, 60, true ); // Corpus Christi (Octavo domingo después de Pascua) - 60 días
		$this->list[] = $this->calculateFromEasterDate($year, 68, true ); // Sagrado Corazón de Jesús (Noveno domingo después de Pascua) 68 días
		
		sort($this->list);
	}
	
	/**
	 * funcion que mueve una fecha diferente a lunes al siguiente lunes en el
	 * calendario y se aplica a fechas que estan bajo la ley emiliani
	 * @param int $month
	 * @param int $day
	 */
	public function moveToMonday2($year, $month, $day) {
		// Número de días a sumar al día para llegar al siguiente lunes
		$daysToAdd = array(
			0 => 1, // Domingo
			1 => 0, // Lunes
			2 => 6, // Martes
			3 => 5, // Miércoles
			4 => 4, // Jueves
			5 => 3, // Viernes
			6 => 2, // Sábado
		);
		
		// Día de la semana original
		$monday = date( "w", mktime( 0, 0, 0, $month, $day, $year ) );
		
		// Lunes siguiente al día original
		$monday += $daysToAdd[$monday];
		
		// Es posible que el mes haya cambiado con la suma de días
		$month = date( "m", mktime( 0, 0, 0, $month, $monday, $year ) ) ;
		
		return date( "d", mktime( 0, 0, 0, $month, $monday, $year ) ) ;
	}

    public function moveToMonday($year, $month, $day) {
        // Crea una instancia de Carbon a partir de la fecha
        $fecha = Carbon::createFromDate($year, $month, $day);

        // Mueve la fecha al siguiente lunes
        $siguienteLunes = $fecha->next(Carbon::MONDAY);
        
        // Devuelve la fecha del lunes en el formato deseado
        return $siguienteLunes->format('Y-m-d');
    }

    private function calculateEasterSunday(int $year): \DateTime
    {
        // Algoritmo de Meeus/Jones/Butcher
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $p = ($h + $l - 7 * $m + 114) % 31;

        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = $p + 1;

        return new \DateTime("{$year}-{$month}-{$day}");
    }

    /**
     * Calcula una fecha basada en el Domingo de Pascua, sumando días.
     *
     * @param int $year El año.
     * @param int $numDays Número de días a sumar al Domingo de Pascua.
     * @param bool $toMonday Si la fecha final debe moverse al siguiente lunes.
     * @return string
     */
    public function calculateFromEasterDate($year, $numDays = 0, $toMonday = false): string
    {
        // 1. Obtén el Domingo de Pascua como un objeto DateTime usando nuestra función personalizada
        
        $year = date("Y");
        $easterSunday = $this->calculateEasterSunday($year);

        // 2. Clona el objeto para evitar modificar la fecha original de Pascua
        $targetDate = clone $easterSunday;

        // 3. Añade el número de días especificado
        if ($numDays !== 0) {
            $targetDate->modify("+{$numDays} days");
        }

        // 4. Obtén el mes y el día de la fecha resultante
        $month = $targetDate->format('m');
        $day = $targetDate->format('d');
        // El año ya lo tenemos, es $year

        // 5. Aplica la lógica de toMonday o retorna la fecha formateada
        // Asumiendo que $this->moveToMonday es un método existente en tu clase
        if ($toMonday) {
            return $this->moveToMonday($year, $month, $day);
        } else {
            return sprintf("%s-%s-%s", $year, $month, $day);
        }
    }
	
	public function isHoliday($date) {
        \Log::info($date);
        \Log::info('List ini: ');
        \Log::info($this->list);
        \Log::info('List fin: ');
		return in_array($date, $this->list);
	}
	
	public function isWeekend($date) {
		$dayWeek = date("w", strtotime($date));
		return $dayWeek == 0 || $dayWeek == 6;
	}
	
	public function isHolidayOrWeekend($date) {
		return $this->isHoliday($date) || $this->isWeekend($date);
	}
}