<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Configuration;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Administrative\License;
use DB;
use App\Administrative\EmployeeEPS;

class ApplicationController extends Controller
{
    public function index()
    {
        return view('application');
    }

    public function multiselect(Request $request){
      if($request->select){
        return $this->multiSelectFormat(Configuration::getConfiguration($request->select));
      }
      return $this->respondHttp500();
    }

    /**
     * Returns an object with the applications and modules permissions 
     * according to the active licenses for the user in session
     *
     * @return array
     */
    public function appsWhithModules()
    {
      return $this->getAppsModules();
    }

    /**
     * Returns an arrangement with all the applications and modules allowed for the user in session
     *
     * @return Array
     */
    public function getCompanies()
    {
      if(Auth::check())
      {
        $companies = Auth::user()->companies()->withoutGlobalScopes()->get();
        $data = [];

        foreach ($companies as $val)
        {
            $license = DB::table('sau_licenses')
                    ->whereRaw('company_id = ? 
                                AND ? BETWEEN started_at AND ended_at', 
                                [$val->pivot->company_id, date('Y-m-d')])
                    ->first();
          
            if ($license)
            {
              $data[$val->pivot->company_id] = [
                  "id"=>$val->pivot->company_id, 
                  "name"=>ucwords(strtolower($val->name))
                ];
            }
        }
        
        return ["selected"=>Session::get('company_id'), "data"=>$data];
      }

      return $this->respondHttp401();
    }

    /**
     * Update the company_id and check if the current route is allowed for the other company of the user, 
     * in case of not having permission, a route with a level lower than the current module is calculated 
     * until arriving at the root of the application
     *
     * @param Request $request
     * @return String
     */
    public function changeCompany(Request $request)
    {
      Session::put('company_id', $request->input('company_id'));

      $new_path = "/";
      $data = $this->getAppsModules();
      $currentPath = trim($request->input('currentPath'), '/');
      $currentPath = explode("/", $currentPath);

      if (COUNT($currentPath) == 1) 
      {
        if (isset($data[$currentPath[0]]) )//Permiso a la aplicacion
          $new_path .= $currentPath[0];
        //ELSE ---> Esta en la raiz o No tiene acceso a la aplicacion
      }
      else
      {
        $app = $currentPath[0];

        if (!isset($data[$app]))//NO tienen Permiso a la aplicacion
          return $new_path;

        $new_path .= $app;
        $modules = explode("-", $request->input('currentName'));

        if (COUNT($modules) == 1) //Link directo
        {
          foreach ($data[$app]["modules"] as $key => $value)
          {
            if(strtolower($value["name"]) == $modules[0])
              return $new_path .= '/'.$modules[0];
          }
        }
        else //Submodulo
        {
          $path_mod = '';

          foreach ($data[$app]["modules"] as $key => $value)
          {
            if(strtolower($value["name"]) == $modules[0]) //Entra si tiene permiso al modulo
            {
              $path_mod = $modules[0];
              $pos_mod = $key;
            }
          }

          if ($path_mod != '')//Si tiene permiso al modulo verifica el submodulo
          {
            foreach ($data[$app]["modules"][$pos_mod]["subModules"] as $key => $value)
            {
              if(strtolower($value["name"]) == $modules[1])
                return $new_path .= '/'.$path_mod.'/'.$modules[1];
            }
          }
        }
      }

      return $new_path;
    }

    /**
     * Returns an array for a group-type input
     *
     * @return Array
     */
    public function multiselectGroupModules()
    {
      $data = $this->getAppsModules();
      $result = [];

      foreach($data as $keyApp => $valueApp)
      {
        foreach ($valueApp["modules"] as $keyModule => $valueModule)
        {
          if (isset($valueModule["subModules"]))
          {
            foreach ($valueModule["subModules"] as $keySubModule => $valueSubModule)
            {
              $result[$valueApp["display_name"]][$valueSubModule["id"]] = $valueSubModule["display_name"];  
            }
          }
          else
          {
            $result[$valueApp["display_name"]][$valueModule["id"]] = $valueModule["display_name"];
          }
        }
      }

      return $this->multiSelectGroupFormat($result);
    }

    /**
     * Returns an arrangement with the sexs
     *
     * @return Array
     */
    public function multiselectSexs()
    {
      $sexs = ["M", "F"];
      return $this->multiSelectFormat($sexs);
    }

    /**
     * Returns an array for a select type eps
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectEps(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $eps = EmployeeEPS::selectRaw("
                    sau_employees_eps.id as id,
                    CONCAT(sau_employees_eps.code, ' - ', sau_employees_eps.name) as name
                ")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                    $query->orWhere('code', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($eps)
            ]);
        }
        else
        {
            $eps = EmployeeEPS::selectRaw("
                sau_employees_eps.id as id,
                CONCAT(sau_employees_eps.code, ' - ', sau_employees_eps.name) as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($eps);
        }
    }
}