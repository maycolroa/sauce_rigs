<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use App\Traits\ContractTrait;
use App\Models\Administrative\Users\User;
use App\Models\General\LogUserActivity;
use App\Models\General\Team;
use Carbon\Carbon;
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
    use ContractTrait;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/';

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
            if (Auth::user()->active == 'SI')
            {
                $companies = Auth::user()->companies()->withoutGlobalScopes()->get();

                if ($companies->count() > 0)
                {
                    foreach ($companies as $val)
                    {
                        $license = DB::table('sau_licenses')
                                ->whereRaw('company_id = ? 
                                            AND ? BETWEEN started_at AND ended_at', 
                                            [$val->pivot->company_id, date('Y-m-d')])
                                ->get();

                        if (COUNT($license) > 0)
                        {
                            Session::put('company_id', $val->pivot->company_id);
                            $team = Team::where('name', Session::get('company_id'))->first()->id;

                            if (!Auth::user()->terms_conditions)
                            {
                                $this->userActivity();
                                
                                return $this->respondHttp200([
                                    'redirectTo' => 'termsconditions'
                                ]);
                            }
                            else
                            {
                                if (Auth::user()->hasRole('Arrendatario', $team) || Auth::user()->hasRole('Contratista', $team))
                                {
                                    $contract = $this->getContractUser(Auth::user()->id);

                                    if ($contract->active == 'SI')
                                    {
                                        if ($contract->completed_registration == 'NO')
                                        {
                                            Auth::user()->update([
                                                'last_login_at' => Carbon::now()->toDateTimeString()
                                            ]);

                                            $this->userActivity();
                                            
                                            return $this->respondHttp200([
                                                'redirectTo' => 'legalaspects/contracts/information'
                                            ]);
                                        }

                                        return $this->defaultUrl();
                                    }
                                    else 
                                    {
                                        Auth::logout();
                                        return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario su contratista se encuentra inhabilitada para poder ingresar al sistema']], 422);
                                    }
                                }

                                return $this->defaultUrl();
                            }
                            
                        }
                    }
                    
                    Auth::logout();
                    return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario debe renovar su licencia para poder ingresar al sistema']], 422);
                }
                else 
                {
                    Auth::logout();
                    return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario no posee una compañia activa para poder ingresar al sistema']], 422);
                }
            }
            else 
            {
                Auth::logout();
                return $this->respondWithError(['errors'=>['email'=>'Usuario inhabilitado']], 422);
            }
        }

        return $this->respondWithError(['errors'=>['email'=>'Email o Contraseña incorrecto']], 422);
    }

    private function defaultUrl()
    {
        Auth::user()->update([
            'last_login_at' => Carbon::now()->toDateTimeString()
        ]);

        $this->userActivity();

        if (Auth::user()->default_module_url)
            return $this->respondHttp200([
                'redirectTo' => strtolower(Auth::user()->default_module_url)
            ]);

        return $this->respondHttp200();
    }

    public function userActivity($description = NULL)
    {
        $activity = new LogUserActivity;
        $activity->user_id = Auth::user()->id;
        $activity->company_id = Session::get('company_id');
        $activity->description = $description ? $description : 'Inicio de sesión';
        $activity->save();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->userActivity('Cerrado de sesión');

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }
}
