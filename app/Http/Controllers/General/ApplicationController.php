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
use App\Models\General\CompanyGroup;
use App\Models\General\LogUserActivity;
use App\Models\General\Module;
use App\Models\General\FiltersState;
use App\Models\General\PageVuetable;
use DB;
use App\Models\Administrative\Employees\EmployeeEPS;
use App\Models\Administrative\Employees\EmployeeAFP;
use App\Models\Administrative\Employees\EmployeeARL;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
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

    public function getContract()
    {
      if (Auth::check())
      {
        $data = ContractLesseeInformation::select(
          'sau_ct_information_contract_lessee.id',
          'sau_ct_information_contract_lessee.social_reason AS name'
        )
        ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
        ->where('sau_user_information_contract_lessee.user_id', Auth::user()->id)
        ->get()
        ->mapWithKeys(function ($item, $key) {
          return [$item->id => $item];
        });

        return collect([
          "selected" => Session::get('contract_id'), 
          "data"     => $data
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

      return $new_path;
    }

    public function changeContract(Request $request)
    {
      Session::put('contract_id', $request->input('contract_id'));

      \Log::info(Session::get('contract_id'));

      $new_path = "/";

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
                ->where('state', true)
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
            ")
            ->where('state', true)
            ->pluck('id', 'name');
        
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
                ->where('state', true)
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
            ")
            ->where('state', true)
            ->pluck('id', 'name');
        
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

    public function setStatePageVuetable(Request $request)
    {
      PageVuetable::updateOrCreate(
          [
            'user_id' => $this->user->id, 
            'vuetable' => $request->vuetable,
            'company_id' => $this->company
          ],
          [
            'user_id' => $this->user->id,
            'company_id' => $this->company,
            'vuetable' => $request->vuetable,
            'page' => $request->page
          ]);

      return $this->respondHttp200([
        'data' => 'ok'
      ]);
    }

    public function getStateFilters(Request $request)
    {
      $filters = FiltersState::where('user_id', Auth::user()->id)->where('url', $request->url)->first();

      if ($filters)
      {
        $filters = json_decode($filters->data, true);

        $hasValues = false;

        foreach ($filters as $key => $filter)
        {
          if ($key == 'filtersType') {
            continue;
          }
          else if (is_array($filter) && COUNT($filter) > 0)
          {
            $hasValues = true;
            break;
          }
          else if ($filter)
          {
            $hasValues = true;
            break;
          }
        }

        $filters['hasValues'] = $hasValues;
      }
      
      return $filters;
    }

    public function getPageVuetable(Request $request)
    {
      $pages = PageVuetable::where('user_id', Auth::user()->id)->where('vuetable', $request->vuetable)->first();

      if ($pages)
      {
        if ($request->vuetable == 'dangerousconditions-inspections-qualification' || $request->vuetable == 'industrialsecure-dangermatrix-history' || $request->vuetable == 'industrialsecure-tags-administrative-control-search')
          $pages = 1;
        else
          $pages = $pages->page;
      }
      else
        $pages = 1;
      
      return $pages;
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

    public function multiselectCompaniesGroup(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $companies = CompanyGroup::select("id", "name")
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
            $companies = CompanyGroup::selectRaw("
              sau_company_groups.id as id,
              sau_company_groups.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($companies);
        }
    }

    public function multiselectCompaniesGroupSpecific(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $id_group = $request->group_id;
            \Log::info($id_group);
            $companies = Company::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->where('company_group_id', $id_group)
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

    public function siNo()
    {
        $data = ["SI"=>"SI", "NO"=>"NO"];
        return $this->multiSelectFormat(collect($data));
    }

    public function userActivity(Request $request)
    {
        $activity = new LogUserActivity;
        $activity->user_id = $this->user->id;
        $activity->company_id = $this->company;
        $activity->description = $request->description;
        $activity->save();
    }

    /**
   * Get text terms and conditions
   *
   * @return \Illuminate\Http\Response
   */
  public function getTermsConditionsUsers()
  {
      return $this->respondHttp200([
          'data' => Configuration::getConfiguration('ph_text_terms_conditions')
      ]);
  }

  public function accepTermsConditionsUsers()
  {
    $user = User::find($this->user->id);
    $user->terms_conditions = true;
    $user->update();
  }
}