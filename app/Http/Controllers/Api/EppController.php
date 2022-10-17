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
use Auth;

class EppController extends ApiController
{
    public function getModuleEpp(Request $request)
    {
      $module = PermissionService::getModuleByName('epp');

      $configuration = ConfigurationCompany::select('value')->where('key', 'inventory_management');
      $configuration->company_scope = $request->company_id;
      $configuration = $configuration->first();

      return $this->respondHttp200([
        'data' => [
          'module_epp' => PermissionService::existsLicenseByModule($request->company_id, $module->id),
          'inventary' => $configuration ? true : false
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

        $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
        ->withoutGlobalScopes()
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->where('location_id', $request->location_id)
        ->where('sau_epp_elements.company_id', $request->company_id)
        ->get()
        ->toArray();

        if ($request->inventary)
        {
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
            $element_disponibles = ElementBalanceLocation::select('element_id')
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
            }

            foreach ($ids_disponibles as $key => $value) {

                $ele = Element::withoutGlobalScopes()->find($value);

                $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                ->where('element_id', $ele->id)
                ->first();

                $content = [
                    'id_ele' => $ele->id,
                    'class' => $ele->class_element,
                    'quantity' => '',
                    'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                    'code' => ''
                ];

                array_push( $elements_id, ['element' => $content, 'options' => []]);
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

            \Log::info(11);
            
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
                  $img_firm = ImageApi::where('hash', $request->firm['image'])->where('type', 3)->first();

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
          if ($file['photo_'.$keyF]['file'])
          {
            $img_firm = ImageApi::where('hash', $file['file'])->where('type', 3)->first();

            $fileUpload = new FileTransactionEmployee();
            $fileUpload->transaction_employee_id = $transaction_id;
            $fileUpload->file = $img_firm->file;
            $fileUpload->save();
          }
        }
    }
}
