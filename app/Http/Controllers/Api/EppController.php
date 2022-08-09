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
      else if ($location_level['headquarter'] == 'SI')
          $employees->where('employee_headquarter_id', $location->employee_headquarter_id);
      else if ($location_level['process'] == 'SI')
          $employees->where('employee_process_id', $location->employee_process_id);
      else
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

    /*public function saveImageApi(Request $request)
    {
      DB::beginTransaction();
          
        try
        {
          $img = $this->base64($request->image);
          $fileName = $img['name'];
          $file = $img['image'];

          $image = new ImageApi;
          $image->file = $fileName;
          $image->type = $request->type;
          $image->hash = $request->hash;
          $image->save();

          if (!$image->save())
                return $this->respondHttp500();

          if ($image->type == 1)
            (new Report)->store_image_api($fileName, $file);
          else
            (new InspectionItemsQualificationAreaLocation)->store_image_api($fileName, $file);

          DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
          'data' => $request->id
        ]);
    }

    public function base64($file)
    {
      $image_64 = $file;

      $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

      $replace = substr($image_64, 0, strpos($image_64, ',')+1); 

      $image = str_replace($replace, '', $image_64); 

      $image = str_replace(' ', '+', $image); 

      $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

      $imagen = base64_decode($image);

      return ['name' => $imageName, 'image' => $imagen];

    } */
}
