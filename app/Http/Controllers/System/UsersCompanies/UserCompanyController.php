<?php

namespace App\Http\Controllers\System\UsersCompanies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;

class UserCompanyController extends Controller
{
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
        ->where('sau_role_user.role_id', '<>', $role->id)
        ->orWhereNull('sau_role_user.role_id')
        ->groupBy('sau_users.id', 'company');

        return Vuetable::of($usersCompanies)
                    ->make();
    }
}
