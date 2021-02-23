<?php

namespace App\Http\Controllers\System\UsersCompanies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Jobs\System\UsersCompanies\UserCompaniesExportJob;
use App\Traits\Filtertrait;

class UserCompanyController extends Controller
{
    use Filtertrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $role = Role::defined()->where('name', 'Superadmin')->first();

        $url = "/system/userscompanies";

        $usersCompanies = User::select(
            'sau_users.id',
            'sau_users.name',
            'sau_users.email', 
            'sau_companies.name as company'
        )
        ->withoutGlobalScopes()
        ->join('sau_company_user', 'sau_users.id', 'sau_company_user.user_id')
        ->join('sau_companies', 'sau_companies.id', 'sau_company_user.company_id')
        ->leftJoin('sau_role_user', function($q){ 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                  ->on('sau_role_user.team_id', '=', 'sau_companies.id');
        })
        ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
        ->leftJoin('sau_permission_role', 'sau_permission_role.role_id', 'sau_roles.id')
        ->leftJoin('sau_permissions', 'sau_permissions.id', 'sau_permission_role.permission_id')
        ->whereRaw("(sau_role_user.role_id <> {$role->id} OR sau_role_user.role_id IS NULL)")
        ->groupBy('sau_users.id', 'company');

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $usersCompanies->inPermissions($this->getValuesForMultiselect($filters["permissions"]), $filters['filtersType']['permissions']);
        }

        return Vuetable::of($usersCompanies)
                    ->make();
    }

    public function export(Request $request)
    {
        try
        {
            $permissions = $this->getValuesForMultiselect($request->permissions);
            $filtersType = $request->filtersType;

            $filters = [
                'permissions' => $permissions,
                'filtersType' => $filtersType
            ];

            UserCompaniesExportJob::dispatch($this->user, $filters, $this->company);
          
            return $this->respondHttp200();
        } 
        catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
