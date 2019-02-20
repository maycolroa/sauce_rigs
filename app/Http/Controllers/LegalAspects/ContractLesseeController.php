<?php

namespace App\Http\Controllers\LegalAspects;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LegalApects\Contracts\ContractRequest;
use App\Models\LegalAspects\ContractLesseeInformation;
use App\User;
use App\Traits\UserTrait;
use Session;
use Illuminate\Support\Facades\Auth;

class ContractLesseeController extends Controller
{
    use UserTrait;
    /**
     * Display index.
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
        $users = User::select(
            'sau_users.*',
            'sau_roles.name as role'
        )
        ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
        ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
        ->where('sau_users.id', '<>', Auth::user()->id);

       return Vuetable::of($users)
                ->make();
    }


    public function store(ContractRequest $request)
    {
        $user = $this->createUser($request);
        $contractLesseeInformation = new ContractLesseeInformation;

        if ($user == $this->respondHttp500() || $user == null) {
            return $this->respondHttp500();
        }

        $user->companies()->sync(Session::get('company_id'));
        $user->syncRoles([$request->get('role_id')]);
        // $contractLesseeInformation

        return $this->respondHttp200([
            'message' => 'Se creo el usuario'
        ]);
        
    }
}
