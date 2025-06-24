<?php

namespace App\Http\Controllers\General;

use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
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
use App\Traits\ContractTrait;
use App\Models\General\Team;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    use ContractTrait;

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

    public function getContractsMultilogin()
    {
      if (Auth::check())
      {
        $data = ContractLesseeInformation::select(
          'sau_ct_information_contract_lessee.id',
          'sau_ct_information_contract_lessee.social_reason AS name'
        )
        ->get()
        ->mapWithKeys(function ($item, $key) {
          return [$item->id => $item];
        });

        return collect([
          "selected" => '', 
          "data"     => $data
        ]);
      }

      return $this->respondHttp401();
    }

    public function isContratante()
    {
        $user = Auth::user();

        if ($user->hasRole('Arrendatario', Session::get('company_id')) || $user->hasRole('Contratista', Session::get('company_id')))
        {
            $rolesOld = DB::table('sau_role_user_multilogin')->where('user_id', $user->id)->count();

            return $rolesOld > 0;
        }

        return true;
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
      if (Auth::user()->hasRole('Arrendatario', Session::get('company_id')) || Auth::user()->hasRole('Contratista', Session::get('company_id')))
      {
        $isContratante = $this->isContratante();

        if ($isContratante)
          $this->returnContratante($request);
      }

      Session::put('company_id', $request->input('company_id'));

      $contract = $this->getContractUserLogin(Auth::user()->id, $request->input('company_id'));

      if ($contract)
        Session::put('contract_id', $contract->id);

      $new_path = "/";

      return $new_path;
    }

    public function changeContract(Request $request)
    {
      Session::put('contract_id', $request->input('contract_id'));

      $new_path = "/";

      return $new_path;
    }

    public function changeContractMultilogin(Request $request)
    {
      $user = Auth::user();

      if (!$user->hasRole('Arrendatario', $this->team) && !$user->hasRole('Contratista', $this->team))
      {
        try
        {
          DB::beginTransaction();

          DB::table('sau_user_information_contract_lessee_multilogin')->where('user_id', $user->id)->delete();
          DB::table('sau_role_user_multilogin')->where('user_id', $user->id)->delete();

          $roles = DB::table('sau_role_user')->where('user_id', $user->id)->get();
          
          $rolesInsert = [];

          foreach ($roles as $role)
          {
            $rolesInsert[] = [
              'role_id' => $role->role_id,
              'user_id' => $role->user_id,
              'user_type' => $role->user_type,
              'team_id' => $role->team_id
            ];
          }

          DB::table('sau_role_user_multilogin')->insert($rolesInsert);

          $contracts = DB::table('sau_user_information_contract_lessee')->where('user_id', $user->id)->get();
          
          $contractsInsert = [];

          foreach ($contracts as $row)
          {
            $contractsInsert[] = [
              'user_id' => $row->user_id,
              'information_id' => $row->information_id
            ];
          }

          DB::table('sau_user_information_contract_lessee_multilogin')->insert($contractsInsert);

          DB::table('sau_user_information_contract_lessee')->where('user_id', $user->id)->delete();
          DB::table('sau_role_user')->where('user_id', $user->id)->delete();

          DB::table('sau_user_information_contract_lessee')->insert(['user_id' => $user->id, 'information_id' => $request->input('contract_id')]);

          $contract = DB::table('sau_ct_information_contract_lessee')->where('id', $request->input('contract_id'))->first();
          $role_name = $contract->type == 'Proveedor' ? 'Contratista' : $contract->type;

          $role = Role::defined()->where('name', $role_name)->first();
          
          $user->syncRoles([$role->id], $this->team);

          Session::put('contract_id', $request->input('contract_id'));

          DB::commit();

        } catch (\Exception $e) {
          DB::rollback();
          \Log::info($e->getMessage());

          return $this->respondHttp500();
        }
      }

      return '';
    }

    public function returnContratante(Request $request)
    {
      $user = Auth::user();

      if ($user->hasRole('Arrendatario', $this->team) || $user->hasRole('Contratista', $this->team))
      {
        try
        {
          DB::beginTransaction();

          DB::table('sau_user_information_contract_lessee')->where('user_id', $user->id)->delete();
          DB::table('sau_role_user')->where('user_id', $user->id)->delete();

          $contracts = DB::table('sau_user_information_contract_lessee_multilogin')->where('user_id', $user->id)->get();
          
          $contractsInsert = [];

          foreach ($contracts as $row)
          {
            $contractsInsert[] = [
              'user_id' => $row->user_id,
              'information_id' => $row->information_id
            ];
          }

          DB::table('sau_user_information_contract_lessee')->insert($contractsInsert);

          $roles = DB::table('sau_role_user_multilogin')->where('user_id', $user->id)->get();

          foreach ($roles as $role)
          {
            $user->attachRole($role->role_id, $role->team_id);
          }

          DB::table('sau_user_information_contract_lessee_multilogin')->where('user_id', $user->id)->delete();
          DB::table('sau_role_user_multilogin')->where('user_id', $user->id)->delete();

          Session::forget('contract_id');

          DB::commit();

        } catch (\Exception $e) {
          DB::rollback();
          \Log::info($e->getMessage());

          return $this->respondHttp500();
        }
      }

      return '';
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

    public function userActivity($description = NULL)
    {
        $activity = new LogUserActivity;
        $activity->user_id = $this->user->id;
        $activity->company_id = $this->company;
        $activity->description = $description ?? 'Acceso al sistema';
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

  private function defaultUrl($ajax = true)
  {
      Auth::user()->update([
          'last_login_at' => Carbon::now()->toDateTimeString()
      ]);

      $this->userActivity('Inicio de Sesion por Código');

      if (Auth::user()->default_module_url)
      {
          if ($ajax)
              return $this->respondHttp200([
                  'redirectTo' => strtolower(Auth::user()->default_module_url)
              ]);
          else
              return redirect(strtolower(Auth::user()->default_module_url));
      }

      if ($ajax)
          return $this->respondHttp200();
      else
          return redirect('/');
  }

  public function verifyCodeLogin(Request $request)
  {
      $user = Auth::user();

      if ($request->code_validation)
      {
          if ($user->code_login == $request->code_validation)
          {
              $user->update([
                  'validate_login' => true,
                  'code_login' => NULL
              ]);

              if (!$user->terms_conditions)
              {
                  $this->userActivity('Inicio de Sesion por Código - Terminos y condiciones');
                  
                  return $this->respondHttp200([
                      'redirectTo' => 'termsconditions'
                  ]);
              }
              else
              {                
                $team = Team::where('name', Session::get('company_id'))->first()->id;

                  if ($user->hasRole('Arrendatario', $team) || $user->hasRole('Contratista', $team))
                  {
                      $contract = $this->getContractUserLogin($user->id);
                      Session::put('contract_id', $contract->id);

                      if ($contract->active == 'SI')
                      {
                          if ($contract->completed_registration == 'NO')
                          {
                              $user->update([
                                  'last_login_at' => Carbon::now()->toDateTimeString()
                              ]);
                              
                              return $this->respondHttp200([
                                  'redirectTo' => 'legalaspects/contracts/information'
                              ]);
                          }

                          return $this->defaultUrl();
                      }
                      else 
                      {
                          $user->update([
                              'validate_login' => false
                          ]);

                          Auth::logout();
                          return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario su contratista se encuentra inhabilitada para poder ingresar al sistema']], 422);
                      }
                  }

                  return $this->defaultUrl();
              }
          }
          else if ($user->code_login != $request->code_validation)
          {
              return $this->respondWithError('Estimado Usuario el código ingresado no coincide con el generado para el inicio de la sesión, por favor verifique y vuelva a intentarlo');
          }
      }
      else
      {
          return $this->respondWithError('Estimado Usuario debe ingresar el código enviado a su correo electronico para continuar con el inicio de sesión');
      }
  }

}
