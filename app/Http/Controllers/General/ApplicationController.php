<?php

namespace App\Http\Controllers\General;

use App\Models\Administrative\Users\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Configuration;
use Illuminate\Support\Facades\Auth;
use Session;
//use App\Models\General\License;
use App\Models\General\Company;
use App\Models\General\Module;
use App\Models\General\FiltersState;
use DB;
use App\Models\Administrative\Employees\EmployeeEPS;
use App\Models\Administrative\Employees\EmployeeAFP;
use App\Models\Administrative\Employees\EmployeeARL;
use App\Vuetable\VuetableColumnManager;
use App\Facades\General\PermissionService;

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
      return PermissionService::getModulesFormatVue(Auth::user(), $this->company);
    }

    /**
     * Returns an arrangement with all the applications and modules allowed for the user in session
     *
     * @return Array
     */
    public function getCompanies()
    {
      if (Auth::check())
      {
        return collect([
          "selected" => Session::get('company_id'), 
          "data"     => PermissionService::getCompaniesActive(Auth::user())
        ]);
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
      /*$data = $this->getAppsModules();
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
      }*/

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
      return $this->multiselectGroupCreateFormat($data);
    }

    /**
     * Returns an array for a group-type input
     *
     * @return Array
     */
    public function multiselectGroupLicenseModules()
    {
      $data = $this->getLicenseAppsModules();
      return $this->multiselectGroupCreateFormat($data);
    }

    private function multiselectGroupCreateFormat($data)
    {
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

    /**
     * Returns an array for a select type afp
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectAfp(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $afp = EmployeeAFP::selectRaw("
                    sau_employees_afp.id as id,
                    CONCAT(sau_employees_afp.code, ' - ', sau_employees_afp.name) as name
                ")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                    $query->orWhere('code', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($afp)
            ]);
        }
        else
        {
            $afp = EmployeeAFP::selectRaw("
                sau_employees_afp.id as id,
                CONCAT(sau_employees_afp.code, ' - ', sau_employees_afp.name) as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($afp);
        }
    }

    /**
     * Returns an array for a select type arl
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectArl(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $arl = EmployeeARL::selectRaw("
                    sau_employees_arl.id as id,
                    CONCAT(sau_employees_arl.code, ' - ', sau_employees_arl.name) as name
                ")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                    $query->orWhere('code', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($arl)
            ]);
        }
        else
        {
            $arl = EmployeeARL::selectRaw("
                sau_employees_arl.id as id,
                CONCAT(sau_employees_arl.code, ' - ', sau_employees_arl.name) as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($arl);
        }
    }

    /**
     * Returns the custom columns for a specific table
     *
     * @param Request $request
     * @return Array
     */
    public function vuetableCustomColumns(Request $request)
    {
      $columnsManager = new VuetableColumnManager($request->get('customColumnsName'));
      return $this->respondHttp200($columnsManager->getColumnsData());
    }

    public function setStateFilters(Request $request)
    {
      FiltersState::updateOrCreate(
          [
            'user_id' => $this->user->id, 
            'url' => $request->url
          ],
          [
            'user_id' => $this->user->id,
            'company_id' => $this->company,
            'url' => $request->url,
            'data' => json_encode($request->get('filters'))
          ]);

      return $this->respondHttp200([
        'data' => 'ok'
      ]);
    }

    public function getStateFilters(Request $request)
    {
      $filters = FiltersState::where('user_id', Auth::user()->id)->where('url', $request->url)->first();

      if ($filters)
        $filters = json_decode($filters->data, true);
      
      return $filters;
    }

    public function multiselectCompanies(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $companies = Company::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($companies)
            ]);
        }
        else
        {
            $companies = Company::selectRaw("
                sau_companies.id as id,
                sau_companies.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($companies);
        }
    }

    public function multiselectModules(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $modules = Module::select("id", "display_name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('display_name', 'like', $keyword);
                })
                ->orderBy('display_name')
                ->take(30)->pluck('id', 'display_name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($modules)
            ]);
        }
        else
        {
            $modules = Module::selectRaw("
                sau_modules.id as id,
                sau_modules.display_name as display_name
            ")
            ->orderBy('display_name')
            ->pluck('id', 'display_name');
        
            return $this->multiSelectFormat($modules);
        }
    }
}