<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use Session;
use DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        {
            $companies = Auth::user()->companies;

            foreach ($companies as $val)
            {
                $license = DB::table('sau_licenses')
                        ->whereRaw('company_id = ? 
                                    AND ? BETWEEN started_at AND ended_at', 
                                    [$val->pivot->company_id, date('Y-m-d')])
                        ->first();

                if ($license)
                {
                    Session::put('company_id', $val->pivot->company_id);
                    return $this->respondHttp200();
                }
            }
            
            Auth::logout();
            return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario debe renovar su licencia para poder ingresar al sistema']], 422);
        }

        return $this->respondWithError(['errors'=>['email'=>'Email o Contraseña incorrecto']], 422);
    }
}
