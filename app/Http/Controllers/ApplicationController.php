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
      if(Auth::check())
      {
        $data = [];
        $licenses = License::whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])->get();
        $arr_sub_mod = [];

        foreach ($licenses as $value)
        {
          $app = $value->module->application;
          $app->name = strtolower($app->name);
          $mod = $value->module;
          $mod->name = strtolower($mod->name);
          $arr_mod = [];

          if (!isset($data[$app->name]))
          {
            $data[$app->name]["display_name"] = $app->display_name;
            $data[$app->name]["image"]        = $app->image;
            $data[$app->name]["modules"]      = [];
          }

          $subMod_name = explode("/", $mod->name);
          $subMod_display_name = explode("/", $mod->display_name);

          if (COUNT($subMod_name) == 1) //Modulo
          {
            array_push($data[$app->name]["modules"], ["name"=>$mod->name, "display_name"=>$mod->display_name]);
          }
          else //Submodulo
          {
            if (!isset($arr_sub_mod[$app->name][$subMod_name[0]]))
            {
              array_push($data[$app->name]["modules"], ["name"=>$subMod_name[0], "display_name"=>$subMod_display_name[0], "subModules"=>[]]);
            }

            $arr_sub_mod[$app->name][$subMod_name[0]][] = [
              "name"=> $subMod_name[1], "display_name" => $subMod_display_name[1]
            ];
          }

          foreach ($data as $keyApp => $value)
          {
            foreach ($value["modules"] as $keyMod => $value2)
            {
              if (isset($value2["subModules"]))
              {
                $data[$keyApp]["modules"][$keyMod]["subModules"] = $arr_sub_mod[$keyApp][$value2["name"]];
              }
            }
          }
        }
        return $data;
      }

      return $this->respondHttp401();
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
      $data = $this->appsWhithModules();
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
}