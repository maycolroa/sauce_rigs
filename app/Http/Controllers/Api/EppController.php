<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\General\Company;
use App\Facades\Configuration;
use App\Http\Requests\Api\CompanyRequiredRequest;
use App\Models\Administrative\Users\User;
use App\Models\General\Team;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Employees\Employee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\Location;
use App\Facades\General\PermissionService;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Http\Requests\IndustrialSecure\Epp\ElementTransactionsRequest;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\IndustrialSecure\Epp\FileTransactionEmployee;
use App\Facades\Mail\Facades\NotificationMail;
use Auth;
use Carbon\Carbon;
use App\Traits\UtilsTrait;
use App\Models\IndustrialSecure\Epp\TagReason;
use App\Models\IndustrialSecure\Epp\EppWastes;
use App\Models\IndustrialSecure\Epp\ReturnDelivery;
use App\Models\IndustrialSecure\Epp\ChangeElement;

class EppController extends ApiController
{
    use UtilsTrait; 

    public function getModuleEpp(Request $request)
    {
      $module = PermissionService::getModuleByName('epp');

      $configuration = ConfigurationCompany::select('value')->where('key', 'inventory_management');
      $configuration->company_scope = $request->company_id;
      $configuration = $configuration->first();

      return $this->respondHttp200([
        'data' => [
          'module_epp' => PermissionService::existsLicenseByModule($request->company_id, $module->id),
          'inventary' => $configuration
        ]
      ]);
    }

    public function getLocation(Request $request)
    {
      $locations = Location::selectRaw("
        sau_epp_locations.id as id,
        sau_epp_locations.name as name
      ");

      $locations->company_scope = $request->company_id;
      $locations = $locations->get();

      $locations_values = [];

      foreach ($locations as $key => $value)
      {
          array_push($locations_values, $value->multiselect());            
      }

      return $this->respondHttp200([
        'data' => $locations_values
      ]);      
    }

    public function getEmployees(Request $request)
    {
      $location = Location::withoutGlobalScopes()->find($request->location_id);

      $location_level = $this->getLocationFormConfModule($request->company_id);

      $employees = Employee::select("*");

      if ($location_level['regional'] == 'SI')
          $employees->where('employee_regional_id', $location->employee_regional_id);
      if ($location_level['headquarter'] == 'SI')
          $employees->where('employee_headquarter_id', $location->employee_headquarter_id);
      if ($location_level['process'] == 'SI')
          $employees->where('employee_process_id', $location->employee_process_id);
      if ($location_level['area'] == 'SI')
          $employees->where('employee_area_id', $location->employee_area_id);


      $employees->company_scope = $request->company_id;
      $employees = $employees->get();

      $employees = $employees->map(function ($item, $keyCompany) use ($request) {
          $elements = [];

          $position = EmployeePosition::withoutGlobalScopes()->find($item->employee_position_id);

          $relation = Element::select('sau_epp_elements.*')
          ->join('sau_employee_position_epp_elements', 'sau_employee_position_epp_elements.element_id', 'sau_epp_elements.id')
          ->where('sau_employee_position_epp_elements.employee_position_id', $position->id);

          $relation->company_scope = $request->company_id;

          $relation = $relation->get();

          foreach ($relation as $key => $value) {
            array_push($elements, $value->multiselect());
          }

          return [
              'id'        => $item->id,
              'name'      => $item->name,
              'position'  => $position->multiselect(),
              'elements'  => $elements
          ];
      });

      return $this->respondHttp200([
        'data' => $employees->values()
      ]);  
    }

    public function getElementsLocation(Request $request)
    {
        $multiselect = [];
        $elements_id = [];
        $elements_no_disponibles = [];

        $configuration = ConfigurationCompany::select('value')->where('key', 'inventory_management');
        $configuration->company_scope = $request->company_id;
        $configuration = $configuration->first();

        if ($configuration->value == 'SI')
        {
            $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
            ->withoutGlobalScopes()
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_id)
            ->where('sau_epp_elements.company_id', $request->company_id)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $request->location_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $disponible_array = [];

            foreach ($disponible as $key => $value) {
                if (!in_array($value['element_balance_id'], $disponible_array)) 
                  array_push($disponible_array, $value['element_balance_id']);
            }

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible_array)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_id)
            ->where('sau_epp_elements.company_id', $request->company_id)
            ->get()
            ->toArray();

            $ids_disponibles = [];

            foreach ($element_disponibles as $key => $value) {
                $ele = Element::withoutGlobalScopes()->find($value['element_id']);

                array_push( $multiselect, $ele->multiselect());
                array_push( $ids_disponibles, $ele->id);
            }

            foreach ($ids_disponibles as $key => $value) {
                $ele = Element::withoutGlobalScopes()->find($value);

                $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                ->where('element_id', $ele->id)
                ->first();

                $disponible = ElementBalanceSpecific::select('id', 'hash')
                ->where('location_id', $request->location_id)
                ->where('state', 'Disponible')
                ->where('element_balance_id', $element_balance->id)
                ->orderBy('id')
                ->pluck('hash', 'hash');

                $content = [
                    'id_ele' => $ele->id,
                    'class' => $ele->class_element,
                    'quantity' => '',
                    'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                    'code' => ''
                ];

                $options = $this->multiSelectFormat($disponible);

                array_push( $elements_id, ['element' => $content, 'options' => $options]);
            }
        }
        else
        {
            \Log::info('entro');
            /*$element_disponibles = ElementBalanceLocation::select('element_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $ids_disponibles = [];

            foreach ($element_disponibles as $key => $value) 
            {
                $ele = Element::withoutGlobalScopes()->find($value['element_id']);

                array_push( $multiselect, $ele->multiselect());
                array_push( $ids_disponibles, $ele->id);
            }*/
            $eles = Element::withoutGlobalScopes()->where('company_id', $request->company_id)->get();

            foreach ($eles as $key => $ele) {
                \Log::info($ele);
                //$ele = Element::withoutGlobalScopes()->find($value);

                /*$element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                ->where('element_id', $ele->id)
                ->first();*/

                $content = [
                    'id_ele' => $ele->id,
                    'class' => $ele->class_element,
                    'quantity' => '',
                    'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                    'code' => ''
                ];

                array_push( $elements_id, ['element' => $content, 'options' => []]);array_push( $multiselect, $ele->multiselect());
            }
        }

        $data = [
            'multiselect' => $multiselect,
            'elements' => $elements_id,
        ];

        return $this->respondHttp200([
          'data' => $data
        ]);
    }

    public function getElementsQuantity(Request $request)
    {
        $report = ElementBalanceLocation::selectRaw("
            sau_epp_elements.id AS id,
            sau_epp_elements.name AS name,
            SUM(IF(sau_epp_elements_balance_specific.state = 'Disponible', 1, 0)) AS quantity_available
        ")
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_ubication.location_id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.element_balance_id', 'sau_epp_elements_balance_ubication.id')
        ->where('sau_epp_elements.company_id', $request->company_id)
        ->where('sau_epp_locations.company_id', $request->company_id)
        ->where('sau_epp_locations.id', $request->location_id)
        ->groupBy('id', 'name')
        ->get();

        return $this->respondHttp200([
          'data' => $report
        ]);
    }

    public function saveDelivery(CompanyRequiredRequest $request)
    {
      if ($request->inventary == 'SI')
          return $this->storeDelivery($request);
      else
          return $this->storeDeliveryNotInventary($request);
    }

    public function storeDelivery(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $employee = Employee::query();
            $employee->company_scope = $request->company_id;
            $employee = $employee->find($request->employee_id['value']);

            $position = EmployeePosition::query();
            $position->company_scope = $request->company_id;
            $position = $position->find($employee->employee_position_id);

            $delivery = new ElementTransactionEmployee();
            $delivery->employee_id = $request->employee_id['value'];
            $delivery->position_employee_id = $position->id;
            $delivery->type = 'Entrega';
            $delivery->observations = $request->observations ? $request->observations : NULL;
            $delivery->location_id = $request->location_id;
            $delivery->company_id = $request->company_id;
            $delivery->class_element = $request->class_element['value'];
            $delivery->edit_firm = count($request->firm) > 0 ? 'SI' : 'NO';
            $nulo = isset($request->firm['type']) ? ($request->firm['type'] == 'Email' ? 'Email' : ($request->firm['type'] == 'Dibujar' ? 'Dibujar' : NULL)) : NULL;
            $delivery->firm_email = $nulo;
            $delivery->email_firm_employee = $nulo ? ($request->firm['type'] == 'Email' ? $request->firm['email'] : NULL) : NULL;
            $delivery->user_id = $this->user->id;

            if(!$delivery->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                ///$element = Element::find($value['id_ele']['value']);

                $element = Element::query();
                $element->company_scope = $request->company_id;
                $element = $element->find($value['id_ele']['value']);

                if ($element)
                {
                    if ($element->identify_each_element)
                    {
                        $disponible = ElementBalanceSpecific::where('hash', $value['code'])->where('location_id', $request->location_id)->first();

                        if ($disponible->state != 'Disponible')
                            return $this->respondHttp422('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        
                        $disponible->state = 'Asignado';
                        $disponible->save();

                        array_push($elements_sync, $disponible->id);

                        $element_balance = ElementBalanceLocation::find($disponible->element_balance_id);

                        $element_balance->quantity_available = $element_balance->quantity_available - 1;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

                        $element_balance->save();
                    }
                    else
                    {
                        $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                        $disponible = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($value['quantity'])->get();

                        if (!$disponible)
                            return $this->respondHttp422('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        else if ($disponible->count() < $value['quantity'])
                            return $this->respondHttp422('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                        foreach ($disponible as $key => $value2) {
                            $value2->state = 'Asignado';
                            $value2->save();
                            array_push($elements_sync, $value2->id);
                        }

                        $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                        $element_balance->save();
                    }
                }
            }

            $delivery->elements()->sync($elements_sync);

            if ($delivery->edit_firm)
            {
                if (isset($request->firm['image']) && $delivery->firm_email == 'Dibujar')
                {
                  $img_firm = ImageApi::where('hash', $request->firm['image'])->where('type', 4)->first();

                  $delivery->firm_employee = $img_firm->file;

                  if(!$delivery->update())
                      return $this->respondHttp500();
                }
                else if ($delivery->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $delivery->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $delivery->id, 'employee' => $employee->id])]])
                        ->company($request->company_id)
                        ->send();
                }
            }

            $files = $request->get('files');

            if (count($files) > 0)
            {
                $this->processFiles($files, $delivery->id);
            }

            DB::commit();

            return $this->respondHttp200([
              'data' => $delivery
            ]);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function storeDeliveryNotInventary(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $employee = Employee::query();
            $employee->company_scope = $request->company_id;
            $employee = $employee->find($request->employee_id['value']);


            $position = EmployeePosition::query();
            $position->company_scope = $request->company_id;
            $position = $position->find($employee->employee_position_id);

            $delivery = new ElementTransactionEmployee();
            $delivery->employee_id = $request->employee_id['value'];
            $delivery->position_employee_id = $position->id;
            $delivery->type = 'Entrega';
            $delivery->observations = $request->observations ? $request->observations : NULL;
            $delivery->location_id = $request->location_id;
            $delivery->company_id = $request->company_id;
            $delivery->class_element = $request->class_element['value'];
            $delivery->edit_firm = count($request->firm) > 0 ? 'SI' : 'NO';            
            $nulo = isset($request->firm['type']) ? ($request->firm['type'] == 'Email' ? 'Email' : ($request->firm['type'] == 'Dibujar' ? 'Dibujar' : NULL)) : NULL;
            $delivery->firm_email = $nulo;
            $delivery->email_firm_employee = $nulo ? ($request->firm['type'] == 'Email' ? $request->firm['email'] : NULL) : NULL;
            $delivery->user_id = $this->user->id;
            
            if(!$delivery->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::query();
                $element->company_scope = $request->company_id;
                $element = $element->find($value['id_ele']['value']);

                if ($element)
                {
                    $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                    for ($i=1; $i <= $value['quantity']; $i++) { 
                        $hash = Hash::make($element_balance->element_id . str_random(30));
                        $product = new ElementBalanceSpecific;
                        $product->hash = $hash;
                        $product->code = $hash;
                        $product->element_balance_id = $element_balance->id;
                        $product->location_id = $element_balance->location_id;
                        $product->expiration_date = $element->days_expired ? $element->days_expired : NULL;
                        $product->state = 'Asignado';
                        $product->save();

                        array_push($elements_sync, $product->id);
                    }

                    $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                    $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                    $element_balance->save();
                }
            }

            $delivery->elements()->sync($elements_sync);

            if ($delivery->edit_firm)
            {
                if (isset($request->firm['image']) && $delivery->firm_email == 'Dibujar')
                {
                  $img_firm = ImageApi::where('hash', $firm['image'])->where('type', 3)->first();

                  $delivery->firm_employee = $img_firm->file;

                  if(!$delivery->update())
                      return $this->respondHttp500();
                }
                else if ($delivery->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $delivery->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $delivery->id, 'employee' => $employee->id])]])
                        ->company($request->company_id)
                        ->send();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $delivery->id);
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([            
          'data' => $delivery
        ]);
    }

    public function processFiles($files, $transaction_id)
    { 
        foreach ($files as $keyF => $file) 
        {
          if ($file['file'])
          {
            $img_firm = ImageApi::where('hash', $file['file'])->where('type', 5)->first();

            $fileUpload = new FileTransactionEmployee();
            $fileUpload->transaction_employee_id = $transaction_id;
            $fileUpload->file = $img_firm->file;
            $fileUpload->save();
          }
        }
    }

    public function getDeliveryEmployee(Request $request)
    {
        $location = Location::withoutGlobalScopes()->find($request->location_id);

        $location_level = $this->getLocationFormConfModule($request->company_id);

        $employees = Employee::select("*");

        if ($location_level['regional'] == 'SI')
            $employees->where('employee_regional_id', $location->employee_regional_id);
        if ($location_level['headquarter'] == 'SI')
            $employees->where('employee_headquarter_id', $location->employee_headquarter_id);
        if ($location_level['process'] == 'SI')
            $employees->where('employee_process_id', $location->employee_process_id);
        if ($location_level['area'] == 'SI')
            $employees->where('employee_area_id', $location->employee_area_id);


        $employees->company_scope = $request->company_id;
        $employees = $employees->get();

        $employees = $employees->map(function ($item, $keyCompany) use ($request) {
            $elements = [];

            $position = EmployeePosition::withoutGlobalScopes()->find($item->employee_position_id);

            $transactions = ElementTransactionEmployee::where('employee_id', $item->id)
            ->where('type', 'Entrega')
            ->WhereNull('state');

            $transactions->company_scope = $request->company_id;
            $transactions = $transactions->get();

            $elements = collect([]);                  
            $multiselect = [];
            $id_balance = [];
            $ids_transactions = [];

            if ($transactions->count() > 0)
            { 
                foreach ($transactions as $key => $transaction) 
                {                    
                    array_push($ids_transactions, $transaction->id);

                    foreach ($transaction->elements as $key => $value)
                    {
                        if ($value->state == 'Asignado')
                        {
                            if (!in_array($value->element_balance_id, $id_balance))
                                array_push($id_balance, $value->element_balance_id);

                            $element_base = Element::withoutGlobalScopes()->find($value->element->element_id);

                            if ($element_base->identify_each_element)
                            {
                                $content = [
                                    'id' => $value->id,
                                    'id_ele' => $element_base->id,
                                    'name' => $element_base->name,
                                    'class_element' => $element_base->class_element,
                                    'balance_id' => $value->element_balance_id,
                                    'type' => 'Identificable',
                                    'quantity' => 1,
                                    'code' => $value->hash,
                                    'multiselect_element' => $element_base->multiselect(),
                                    'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                                    'wastes' => 'NO'
                                ];

                                $elements->push($content);
                                array_push($multiselect, $element_base->multiselect());
                            }
                            else
                            {
                                $content = [
                                    'id' => $value->id,
                                    'id_ele' => $element_base->id,
                                    'name' => $element_base->name,
                                    'class_element' => $element_base->class_element,
                                    'balance_id' => $value->element_balance_id,
                                    'quantity' => 1,
                                    'type' => 'No Identificable',
                                    'code' => $value->hash,
                                    'multiselect_element' => $element_base->multiselect(),
                                    'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                                    'wastes' => 'NO'
                                ];

                                //array_push($elements, $content);
                                $elements->push($content);
                                array_push( $multiselect, $element_base->multiselect());
                            }
                        }                                
                    }
                }
            }

            $ids_saltar = [];
            $new = [];

            foreach ($id_balance as $key => $id) 
            {
                $record = $elements->where('balance_id', $id);

                $cantidad = $elements->where('balance_id', $id)->where('type', 'No Identificable')->sum('quantity');
                $codes = [];

                foreach ($record as $key => $hash) 
                {
                    array_push($codes, $hash['code']);
                }

                foreach ($record as $key => $value) 
                {
                    if ($value['type'] == 'Identificable')
                    {
                        $disponible = ElementBalanceSpecific::select('id', 'hash')
                                ->where('location_id', $request->location_id)
                                ->where('state', 'Disponible')
                                ->where('element_balance_id', $value['balance_id'])
                                ->orderBy('id')
                                ->pluck('hash', 'hash');

                        $content = [
                            'id' => $value['id'],
                            'id_ele' => $value['id_ele'],
                            'name' => $value['name'],
                            'class_element' => $value['class_element'],
                            'quantity' => 1,
                            'type' => $value['type'],
                            'code' => $value['code'],
                            'multiselect_element' => $value['multiselect_element'],
                            'key' => $value['key'],
                            'wastes' => 'NO',
                            'rechange' => 'NO'
                        ];

                        $options = $this->multiSelectFormat($disponible);

                        //$elements->push(['element' => $content, 'options' => $options]);
                        array_push($new, ['element' => $content, 'options' => $options]);
                    }
                    else
                    {
                        if (!in_array($value['balance_id'], $ids_saltar))
                        {
                            $content = [
                                'id' => $value['id'],
                                'id_ele' => $value['id_ele'],
                                'name' => $value['name'],
                                'class_element' => $value['class_element'],
                                'quantity' => $cantidad,
                                'type' => $value['type'],
                                'code' => implode(',', $codes),
                                'multiselect_element' => $value['multiselect_element'],
                                'key' => $value['key'],
                                'wastes' => 'NO',
                                'rechange' => 'NO'
                            ];

                            array_push($ids_saltar, $value['balance_id']);
                            array_push($new, ['element' => $content, 'options' => []]);
                        }
                    }
                }
            }

            return [
                'id'        => $item->id,
                'name'      => $item->name,
                'position'  => $position->multiselect(),
                'elements'  => $new,
                'ids_transactions' => $ids_transactions
            ];
        });

        return $this->respondHttp200([
        'data' => $employees->values()
        ]);
    }

    public function storeReturns(Request $request)
    {
        \Log::info($request);
        DB::beginTransaction();

        try
        {
            \Log::info(1);
            $employee = Employee::query();
            $employee->company_scope = $request->company_id;
            $employee = $employee->find($request->employee_id['value']);

            $position = EmployeePosition::query();
            $position->company_scope = $request->company_id;
            $position = $position->find($employee->employee_position_id);

            $returns = new ElementTransactionEmployee();
            $returns->employee_id = $request->employee_id['value'];
            $returns->position_employee_id = $position->id;
            $returns->type = 'Devolucion';
            $returns->observations = $request->observations ? $request->observations : NULL;
            $returns->location_id = $request->location_id;
            $returns->company_id = $request->company_id;
            $returns->class_element = $request->class_element['value'];
            $returns->edit_firm = count($request->firm) > 0 ? 'SI' : 'NO';
            $nulo = isset($request->firm['type']) ? ($request->firm['type'] == 'Email' ? 'Email' : ($request->firm['type'] == 'Dibujar' ? 'Dibujar' : NULL)) : NULL;
            $returns->firm_email = $nulo;
            $returns->email_firm_employee = $nulo ? ($request->firm['type'] == 'Email' ? $request->firm['email'] : NULL) : NULL;
            $returns->user_id = $this->user->id;
            
            if(!$returns->save())
                return $this->respondHttp500();

            \Log::info(2);

            $elements_sync = [];
            $elements_sync_rechange = [];
            $elements_change = [];

            foreach ($request->elements_id as $key => $value) 
            {
                \Log::info(3);
                $element = Element::withoutGlobalScopes()->find($value['id_ele']);
                \Log::info($value['id_ele']);
                \Log::info($request->location_id);
                $element_balance = ElementBalanceLocation::where('element_id', $value['id_ele'])->where('location_id', $request->location_id)->first();

                if (!$element_balance)
                {
                    $element_balance = new ElementBalanceLocation();
                    $element_balance->element_id = $element->id;
                    $element_balance->location_id = $request->location_id;
                    $element_balance->quantity = 0;
                    $element_balance->quantity_available = 0;
                    $element_balance->quantity_allocated = 0;
                    $element_balance->save();
                }
                \Log::info('element_balance '.$element_balance->id);

                if ($element) 
                {
                    \Log::info(4);
                    if ($element->identify_each_element)
                    {
                        $disponible = ElementBalanceSpecific::where('hash', $value['code'])->first();

                        $balance_origen = ElementBalanceLocation::find($disponible->element_balance_id);
                
                        if ($value['rechange'] == 'SI')
                        {
                            \Log::info(5);
                            $reason = TagReason::firstOrCreate(
                                [
                                    'name' => $value['reason'],
                                    'company_id' => $request->company_id
                                ], 
                                [
                                    'name' => $value['reason'],
                                    'company_id' => $request->company_id
                                ]
                            );
                            
                            if ($value['wastes'] == 'SI')
                            {
                                \Log::info(6);
                                $disponible->state = 'Desechado';
                                $disponible->location_id = $request->location_id;
                                $disponible->element_balance_id = $element_balance->id;
                                $disponible->save();

                                $desecho = new EppWastes;
                                $desecho->company_id = $this->company;
                                $desecho->employee_id = $request->employee_id['value'];
                                $desecho->element_id = $disponible->id;
                                $desecho->location_id = $request->location_id;
                                $desecho->code_element = $disponible->hash;
                                $desecho->save();

                                $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;
                            }
                            else
                            {
                                \Log::info(7);
                                $disponible->state = 'Disponible';
                                $disponible->element_balance_id = $element_balance->id;
                                $disponible->location_id = $request->location_id;
                                $disponible->save();

                                $element_balance->quantity_available = $element_balance->quantity_available + 1;
                                $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;
                            }

                            array_push($elements_sync, $disponible->id);

                            \Log::info(8);

                            $new_product = ElementBalanceSpecific::where('hash', $value['code_new'])->first();

                            if ($new_product->state = 'Disponible')
                            {
                                \Log::info(9);
                                $new_product->state = 'Asignado';
                                $new_product->location_id = $request->location_id;
                                $new_product->element_balance_id = $element_balance->id;
                                $new_product->save();

                                $change = [
                                    'code_new' => $new_product->id,
                                    'code_old' => $disponible->id,
                                    'element_id' => $element->id,
                                    'reason' => $reason->implode(',')
                                ];

                                array_push($elements_change, $change);

                                $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;
                                $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

                                array_push($elements_sync_rechange, $new_product->id);
                            }
                        }
                        else
                        {
                            \Log::info(10);
                            $disponible->state = 'Disponible';
                            $disponible->location_id = $request->location_id;
                            $disponible->element_balance_id = $element_balance->id;
                            $disponible->save();

                            $element_balance->quantity_available = $element_balance->quantity_available + 1;
                            $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                            $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

                            array_push($elements_sync, $disponible->id);
                        }
                    }
                    else
                    {
                        \Log::info(11);
                        $codigos = explode(',' , $value['code']);

                        $count_codes = COUNT($codigos);

                        $quantity_return = $value['quantity'];

                        $codes_returns = [];

                        foreach ($codigos as $key => $code) 
                        {
                            $key = $key + 1;

                            if ($key <= $quantity_return)
                                array_push($codes_returns, $code);
                        }
                        \Log::info(12);
                        
                        $count_codes_returns = COUNT($codes_returns);

                        $balance_id = ElementBalanceSpecific::whereIn('hash', $codes_returns)->where('state', 'Asignado')->first();

                        $balance_origen = ElementBalanceLocation::find($balance_id->element_balance_id);

                        if ($value['wastes'] == 'SI')
                        {
                            \Log::info(13);
                            if ($value['quantity_waste'] <= $count_codes_returns)
                            {
                                \Log::info(14);
                                for ($i=1; $i <= $value['quantity_waste']; $i++) 
                                { 
                                    $new_product = ElementBalanceSpecific::whereIn('hash', $codes_returns)->where('state', 'Asignado')->first();

                                    $new_product->state = 'Desechado';
                                    $new_product->save();

                                    $desecho = new EppWastes;
                                    $desecho->company_id = $request->company_id;
                                    $desecho->employee_id = $request->employee_id['value'];
                                    $desecho->element_id = $new_product->id;
                                    $desecho->location_id = $request->location_id;
                                    $desecho->code_element = $new_product->hash;
                                    $desecho->save();

                                    $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                    $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

                                    array_push($elements_sync, $new_product->id);
                                }
                            }
                            else
                            {
                                return $this->respondWithError('No puede desechar una cantidad superior a la asignada del elemento ' . $element->name);
                            }
                        }

                        \Log::info(15);

                        if ($value['rechange'] == 'SI')
                        {

                            \Log::info(16);
                            $reason = TagReason::firstOrCreate(
                                [
                                    'name' => $value['reason'],
                                    'company_id' => $request->company_id
                                ], 
                                [
                                    'name' => $value['reason'],
                                    'company_id' => $request->company_id
                                ]
                            );

                            if ($value['quantity_rechange'] > 0)
                            {
                                \Log::info(17);
                                if ($value['quantity_rechange'] <= $count_codes_returns)
                                {
                                    \Log::info(18);
                                    for ($i=1; $i <= $value['quantity_rechange']; $i++) 
                                    { 
                                        $new_product = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->first();

                                        if ($new_product)
                                        {
                                            $new_product->state = 'Asignado';
                                            $new_product->save();

                                            $element_balance->quantity_available = $element_balance->quantity_available - 1;
                                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;
                                        }
                                        else
                                        {
                                            if ($request->inventary == 'NO')
                                            {
                                                $hash = Hash::make($element_balance->element_id . str_random(30));
                                                $product = new ElementBalanceSpecific;
                                                $product->hash = $hash;
                                                $product->code = $hash;
                                                $product->element_balance_id = $element_balance->id;
                                                $product->location_id = $element_balance->location_id;
                                                $product->state = 'Asignado';
                                                $product->save();
                                            }
                                            else
                                                return $this->respondWithError('No existen unidades disponibles del elemento ' . $element->name);
                                        }


                                        $old_product = ElementBalanceSpecific::whereIn('hash', $codigos)->first();

                                        if ($old_product->state != 'Desechado')
                                        {
                                            $old_product->state = 'Disponible';
                                            $old_product->location_id = $request->location_id;
                                            $old_product->element_balance_id = $element_balance->id;
                                            $old_product->save();

                                            $element_balance->quantity_available = $element_balance->quantity_available + 1;
                                            $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                        }                                        

                                        $change = [
                                            'code_new' => $new_product->id,
                                            'code_old' => $old_product->id,
                                            'element_id' => $element->id,
                                            'reason' => $reason
                                        ];

                                        array_push($elements_change, $change);

                                        array_push($elements_sync_rechange, $new_product->id);
                                    }

                                    \Log::info(19);
                                    $old_products = ElementBalanceSpecific::whereIn('hash', $codigos)->get();

                                    foreach ($old_products as $key => $old) 
                                    {
                                        if (!$old->state = 'Desechado')
                                        {
                                            $old->state = 'Disponible';
                                            $old->location_id = $request->location_id;
                                            $old->element_balance_id = $element_balance->id;
                                            $old->save();
                                        }

                                        array_push($elements_sync, $old->id);
                                    }
                                }
                                else
                                {
                                    return $this->respondWithError('No puede hacer un recambio de una cantidad superior a la asignada del elemento ' . $element->name);
                                }
                            }
                        }

                        \Log::info(20);

                        if ($value['wastes'] == 'NO' && $value['rechange'] == 'NO')
                        {
                            \Log::info(21);
                            $old_products = ElementBalanceSpecific::whereIn('hash', $codes_returns)->get();

                            foreach ($old_products as $key => $old) 
                            {
                                $old->state = 'Disponible';
                                $old->location_id = $request->location_id;
                                $old->element_balance_id = $element_balance->id;
                                $old->save();

                                array_push($elements_sync, $old->id);
                            }

                            $element_balance->quantity_available = $element_balance->quantity_available + $value['quantity'];
                            $balance_origen->quantity_available = $balance_origen->quantity_available - $value['quantity'];
                        }
                    }
                }
            }

            $element_balance->save();
            $balance_origen->save();

            \Log::info(22);

            if (COUNT($elements_sync_rechange) > 0)
            {
                $new_delivery = new ElementTransactionEmployee;
                $new_delivery->employee_id = $request->employee_id['value'];
                $new_delivery->position_employee_id = $position->id;
                $new_delivery->type = 'Entrega';
                $new_delivery->observations = NULL;
                $new_delivery->location_id = $request->location_id;
                $new_delivery->company_id = $request->company_id;
                $new_delivery->user_id = $this->user->id;
                $new_delivery->save();

                $new_delivery->elements()->sync($elements_sync_rechange);

                \Log::info(23);
            }

            if (COUNT($elements_change) > 0)
            {
                \Log::info(24);
                foreach ($elements_change as $key => $change) 
                {
                    $rechange = new ChangeElement;
                    $rechange->transaction_employee_id = $returns->id;
                    $rechange->transaction_delivery_id = $new_delivery->id;
                    $rechange->element_id = $change['element_id'];
                    $rechange->element_specific_old_id = $change['code_old'];
                    $rechange->element_specific_new_id = $change['code_new'];
                    $rechange->company_id = $request->company_id;
                    $rechange->user_id = $this->user->id;
                    $rechange->reason = $change['reason'];
                    $rechange->save();
                }
            }
            \Log::info(25);

            $returns->elements()->sync($elements_sync);

            if ($returns->edit_firm)
            {
                if (isset($request->firm['image']) && $returns->firm_email == 'Dibujar')
                {
                  $img_firm = ImageApi::where('hash', $request->firm['image'])->where('type', 4)->first();

                  $returns->firm_employee = $img_firm->file;

                  if(!$returns->update())
                      return $this->respondHttp500();
                }
                else if ($returns->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $returns->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $returns->id, 'employee' => $employee->id])]])
                        ->company($request->company_id)
                        ->send();
                }
            }

            \Log::info(26);

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $returns->id);
            }

            foreach ($request->ids_transactions as $key => $value) 
            {
                \Log::info($value);
                $delivery = ElementTransactionEmployee::withoutGlobalScopes()->find($value);
                \Log::info($delivery);

                $delivery_disponibles = $delivery->elements()->where('state', 'Asignado')->get();

                if ($delivery && $delivery_disponibles->count() == 0)
                {
                    $returnDelivery = new ReturnDelivery;
                    $returnDelivery->transaction_employee_id = $returns->id;
                    $returnDelivery->delivery_id = $value;
                    $returnDelivery->save();

                    $delivery->state = 'Procesada';
                    $delivery->save();
                }
            }

            \Log::info(27);

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //DB::rollback();
            return $this->respondHttp500();
        }

        \Log::info(28);

        return $this->respondHttp200([
            'data' => $returns
        ]);
    }
}
