<?php

namespace App\Traits;

use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\Administrative\Users\User;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use DB;
use Carbon\Carbon;
use Session;
//use App\Models\LegalAspects\Contracts\LiskCheckResumen;

trait ContractTrait
{
    public function getUsersContract($contract_id, $company_id = null, $scope_active = true)
    {
        if (!is_numeric($contract_id))
            throw new \Exception('Contract invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_user_information_contract_lessee.user_id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_ct_information_contract_lessee.id', $contract_id);

        if ($company_id)
            $contract->company_scope = $company_id;

        $contract = $contract->get();
        $users = collect([]);

        if ($contract)
        {
            $users_id = $contract->toArray();
            
            if ($scope_active)
                $users = User::whereIn('id', $users_id)->get();
            else
                $users = User::active()->whereIn('id', $users_id)->get();
        }

        return $users->unique();
    }

    public function getUserMasterContract($contract, $company_id = null, $scope_active = true)
    {
        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $user_master = NULL;

        if ($contract->user_sst_id)
        {
            $user_master = User::find($contract->user_sst_id);
        }
        else
        {
            $user_contract = ContractLesseeInformation::select(
                    'sau_user_information_contract_lessee.user_id')
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
                ->where('sau_ct_information_contract_lessee.id', $contract->id);

            if ($company_id)
                $user_contract->company_scope = $company_id;

            $user_contract = $user_contract->first();
            $users = collect([]);

            if ($user_contract)
            {                
                if ($scope_active)
                    $user_master = User::find($user_contract->user_id);
                else
                    $user_master = User::active()->find($user_contract->user_id);
            }
        }

        return $user_master;
    }

    public function getContractUserLogin($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.*'
            )
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id);

        if ($company_id)
        {
            $active_company = DB::table('sau_company_user')
                ->where('company_id', $company_id)
                ->where('user_id', $user_id)
                ->where('active', 'SI')
                ->first();

            if ($active_company)
                $contract->company_scope = $company_id;
            else
                return NULL;
        }

        $contract = $contract->first();

        return $contract ? $contract : NULL;
    }

    public function getContractUser($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        if (Session::get('contract_id'))
        {
            $contract = ContractLesseeInformation::select(
                    'sau_ct_information_contract_lessee.*'
                )
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
                ->where('sau_user_information_contract_lessee.user_id', $user_id)
                ->where('sau_ct_information_contract_lessee.id', Session::get('contract_id'));

            if ($company_id)
                $contract->company_scope = $company_id;

            $contract = $contract->first();

            return $contract ? $contract : NULL;
        }
        else
            return $this->getContractUserLogin($user_id);
    }


    public function getMultiplesContracstUser($user_id, $multiple = false, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.*'
            )
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id);

        if (!$multiple)
        {
            if ($company_id)
                $contract->company_scope = $company_id;
        }
        else
            $contract = $contract->withoutGlobalScopes();

        $contract = $contract->get();

        return $contract ? $contract : NULL;
    }

    public function getContractIdUser($user_id, $company_id = null)
    {
        if (!is_numeric($user_id))
            throw new \Exception('User invalid');

        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $contract = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.id AS id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_user_information_contract_lessee.user_id', $user_id)
            ->where('sau_ct_information_contract_lessee.id', Session::get('contract_id'));

        if ($company_id)
            $contract->company_scope = $company_id;

        $contract = $contract->first();

        return $contract ? $contract->id : NULL;
    }

    public function getUsersMasterContract($company_id = null)
    {
        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');
            
        $users = User::select('sau_users.*')
            ->active()
            //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->leftJoin('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
            ->leftJoin('sau_role_user', function($q) use ($company_id) { 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                ->on('sau_role_user.team_id', '=', DB::raw($company_id));
            })
            ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->whereNotIn('sau_roles.id', [8,9,5])
            ->whereNull('sau_user_information_contract_lessee.information_id')
            ->orderBy('name');
            //->get();

        if ($company_id)
            $users->company_scope = $company_id;

        $users = $users->get();

        return $users;
    }

    public function getStandardItemsContract($contract)
    {
        $sql = SectionCategoryItems::select(
            'sau_ct_section_category_items.*',
            'sau_ct_standard_classification.standard_name as name'
        )
        ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
        ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id');

        $items = [];

        if ($contract->classification == 'UPA')
        {
            if ($contract->number_workers <= 10)
            {
                if ($contract->risk_class == "Clase de riesgo I" || $contract->risk_class == "Clase de riesgo II" || $contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '3 estandares')->get();
                }
                else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
        }
        else if ($contract->classification == 'Empresa')
        {
            if ($contract->number_workers <= 10)
            {
                if ($contract->risk_class == "Clase de riesgo I" || $contract->risk_class == "Clase de riesgo II" || $contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '7 estandares')->get();
                }
                else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->number_workers > 10 && $contract->number_workers <= 50)
            {
                if ($contract->risk_class == "Clase de riesgo I" || $contract->risk_class == "Clase de riesgo II" || $contract->risk_class == "Clase de riesgo III")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '21 estandares')->get();
                }
                else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
                {
                    $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
                }
            }
            else if ($contract->risk_class == "Clase de riesgo IV" || $contract->risk_class == "Clase de riesgo V")
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
            else if ($contract->number_workers > 50)
            {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();            
            }
        }

        return $items;
    }

    public function reloadLiskCheckResumen($contract, $qualification)
    {
        $items = [];
        $items_delete = [];
        
        if ($contract->type == 'Contratista' || $contract->type == 'Proveedor')
            $items = $this->getStandardItemsContract($contract);
        
        $items_delete = COUNT($items) > 0 ? $items->pluck('id') : [];

        $contract->listCheckResumen()->where('list_qualification_id', $qualification)->delete();

        $items_delete = ItemQualificationContractDetail::select(
                    'sau_ct_item_qualification_contract.*',
                    'sau_ct_qualifications.name AS name'
                )
                ->join('sau_ct_qualifications', 'sau_ct_qualifications.id', 'sau_ct_item_qualification_contract.qualification_id')
                ->where('contract_id', $contract->id)
                ->where('list_qualification_id', $qualification)
                ->whereNotIn('item_id', $items_delete)
                ->get();

        foreach ($items_delete as $item)
        {
            if ($item->name == 'C')
            {
                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.file AS file'
                  )
                  ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                  ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                  ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                  ->where('sau_ct_file_item_contract.item_id', $item->item_id)
                  ->where('sau_ct_file_item_contract.list_qualification_id', $qualification)
                  ->get();
                
                foreach ($files as $file)
                {
                    FileUpload::find($file->id)->delete();
                    Storage::disk('s3')->delete('legalAspects/files/'. $file->file);
                }
            } 
            else if ($item->name == 'NC')
            {
                ActionPlan::model($item)->modelDeleteAll();
            }

            ItemQualificationContractDetail::find($item->id)->delete();
        }

        if (COUNT($items) > 0)
        {
            $totales = [
                'list_qualification_id' => $qualification,
                'total_standard' => 0,
                'total_c' => 0,
                'total_nc' => 0,
                'total_sc' => 0,
                'total_p_c' => 0,
                'total_p_nc' => 0
            ];

            $qualifications = Qualifications::pluck("name", "id");

            try
            {
                $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
                
            } catch (\Exception $e) {
                $exist = 'NO';
            }

            //Obtiene los items calificados
            $items_calificated = ItemQualificationContractDetail::
                      where('contract_id', $contract->id)
                    ->where('list_qualification_id', $qualification);                   

            if ($exist == 'SI')
                $items_calificated->where('state_aprove_qualification', 'APROBADA');

            $items_calificated = $items_calificated->pluck("qualification_id", "item_id");

            $items->each(function($item, $index) use ($qualifications, $items_calificated, $contract, &$totales) {
                
                $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                
                $totales['total_standard']++;

                if ($item->qualification == 'C' || $item->qualification == 'NA')
                {
                    $totales['total_c']++;
                }
                else if ($item->qualification == 'NC')
                {
                    $totales['total_nc']++;
                }
                else
                {
                    $totales['total_nc']++;
                    $totales['total_sc']++;
                }

                return $item;
            });

            $totales['total_p_c'] = round(($totales['total_c'] / $totales['total_standard']) * 100, 1);
            $totales['total_p_nc'] = round(($totales['total_nc'] / $totales['total_standard']) * 100, 1);

            $contract->listCheckResumen()->updateOrCreate(['contract_id'=>$contract->id, 'list_qualification_id' => $qualification], $totales);
        }
    }

    public function reloadLiskCheckResumenPercentege($contract, $qualification)
    {
        $contract->listCheckResumen()->where('list_qualification_id', $qualification)->delete();

        if ($contract->type == 'Contratista' || $contract->type == 'Proveedor')
            $items = $this->getStandardItemsContract($contract);
            
        if (COUNT($items) > 0)
        {
            $totales = [
                'list_qualification_id' => $qualification,
                'total_standard' => 0,
                'total_c' => 0,
                'total_nc' => 0,
                'total_sc' => 0,
                'total_p_c' => 0,
                'total_p_nc' => 0
            ];

            $qualifications = Qualifications::pluck("name", "id");

            try
            {
                $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
                
            } catch (\Exception $e) {
                $exist = 'NO';
            }

            //Obtiene los items calificados
            $items_calificated = ItemQualificationContractDetail::
                      where('contract_id', $contract->id)
                    ->where('list_qualification_id', $qualification);                   

            if ($exist == 'SI')
                $items_calificated->where('state_aprove_qualification', 'APROBADA');

            $items_calificated = $items_calificated->pluck("qualification_id", "item_id");

            $items->each(function($item, $index) use ($qualifications, $items_calificated, $contract, &$totales) {
                
                $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                
                $totales['total_standard']++;

                if ($item->qualification == 'C' || $item->qualification == 'NA')
                {
                    $totales['total_c']++;
                }
                else if ($item->qualification == 'NC')
                {
                    $totales['total_nc']++;
                }
                else
                {
                    $totales['total_nc']++;
                    $totales['total_sc']++;
                }

                return $item;
            });

            $totales['total_p_c'] = round(($totales['total_c'] / $totales['total_standard']) * 100, 1);
            $totales['total_p_nc'] = round(($totales['total_nc'] / $totales['total_standard']) * 100, 1);

            $contract->listCheckResumen()->updateOrCreate(['contract_id'=>$contract->id, 'list_qualification_id' => $qualification], $totales);
        }
    }

    public function calculateDaySocialSecurityExpired($contract)
    {
        $day_expiration = Carbon::parse($this->calculateFirstDayOfMonth());

        $num_days = $contract->social_security_working_day ?? NULL;

        $this->calculateForYear(date("Y"));

        \Log::info($this->list);

        for ($i=0; $i < $num_days; $i++) 
        { 
            ///Elimine la condicion de que si es el ultimo dia no sume un dia mas if ($i == $num_days - 1)

            if ($i == $num_days)
                $day_expiration = $day_expiration;
            else
                $day_expiration = $day_expiration->addDay(1);

            $day_week = $day_expiration->dayOfWeek;

            if ($day_week == 6)
                $day_expiration = $day_expiration->addDay(2);
            else if ($day_week == 0)
                $day_expiration = $day_expiration->addDay(1);

            $is_holiday = $this->isHoliday($day_expiration->format('Y-m-d'));

            \Log::info($is_holiday);

            if ($is_holiday)
                $day_expiration = $day_expiration->addDay(1);
            
            \Log::info('fin: '.$day_expiration->format('Y-m-d'));
        }

        return $day_expiration->format('Y-m-d');   
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