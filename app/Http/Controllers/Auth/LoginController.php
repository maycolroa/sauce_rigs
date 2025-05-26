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
use App\Facades\Mail\Facades\NotificationMail;

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

    function getCodeLogin($longitud): string
    {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $longitudCaracteres = strlen($caracteres);

        $codigo = '';

        for ($i = 0; $i < $longitud; $i++) 
        {
            $indiceAleatorio = rand(0, $longitudCaracteres - 1);
            $codigo .= $caracteres[$indiceAleatorio];
        }
        
        return $codigo;
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        {
            if (Auth::user()->active == 'SI')
            {
                $companies = Auth::user()->companies()->withoutGlobalScopes()->get();
                $user = Auth::user();

                if ($companies->count() > 0)
                {
                    foreach ($companies as $val)
                    {
                        if ($val->pivot->active == 'NO')
                            continue;

                        $license = DB::table('sau_licenses')
                                ->whereRaw('company_id = ? 
                                            AND ? BETWEEN started_at AND ended_at', 
                                            [$val->pivot->company_id, date('Y-m-d')])
                                //->where('freeze', DB::raw("'NO'"))
                                ->get();

                        if (COUNT($license) > 0)
                        {
                            Session::put('company_id', $val->pivot->company_id);
                            $team = Team::where('name', Session::get('company_id'))->first()->id;

                            if (!$user->code_login)
                            {
                                $code = $this->getCodeLogin(6);

                                Auth::user()->update([
                                    'validate_login' => false,
                                    'code_login' => $code
                                ]);

                                NotificationMail::
                                    subject('Código para inicio de sesión')
                                    ->message("Usted ha intentado iniciar sesión en nuestro sistema, por favor ingrese el siguiente codigo para completar su inicio de sesión: {$code}")
                                    ->recipients($user)
                                    ->module('users')
                                    ->company(Session::get('company_id'))
                                    ->send();

                                return $this->respondHttp200([
                                    'redirectTo' => 'codelogin'
                                ]);
                            }
                            else
                            {
                                if ($user->validate_login)
                                {
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
                                            $contract = $this->getContractUserLogin(Auth::user()->id);
                                            Session::put('contract_id', $contract->id);
        
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
                                                Auth::user()->update([
                                                    'validate_login' => false
                                                ]);

                                                Auth::logout();
                                                return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario su contratista se encuentra inhabilitada para poder ingresar al sistema']], 422);
                                            }
                                        }
        
                                        return $this->defaultUrl();
                                    }
                                }
                                else
                                {
                                    return $this->respondHttp200([
                                        'redirectTo' => 'codeLogin'
                                    ]);
                                }
                            }                            
                        }
                    }
                    
                    Auth::user()->update([
                        'validate_login' => false
                    ]);

                    Auth::logout();
                    return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario debe renovar su licencia para poder ingresar al sistema']], 422);
                }
                else 
                {
                    Auth::user()->update([
                        'validate_login' => false
                    ]);

                    Auth::logout();
                    return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario no posee una compañia activa para poder ingresar al sistema']], 422);
                }
            }
            else 
            {
                Auth::user()->update([
                    'validate_login' => false
                ]);

                Auth::logout();
                return $this->respondWithError(['errors'=>['email'=>'Usuario inhabilitado']], 422);
            }
        }

        return $this->respondWithError(['errors'=>['email'=>'Email o Contraseña incorrecto']], 422);
    }

    private function defaultUrl($ajax = true)
    {
        Auth::user()->update([
            'last_login_at' => Carbon::now()->toDateTimeString()
        ]);

        $this->userActivity();

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

    public function userActivity($description = NULL)
    {
        if (!Session::get('company_id'))
        return;
      
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
        Auth::user()->update([
            'validate_login' => false,
            'code_login' => NULL
        ]);

        $this->userActivity('Cerrado de sesión');

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    public function authToken($token)
    {
        $user = User::where('api_token', $token)->withoutGlobalScopes()->first();

        if ($user)
        {
            Auth::login($user);

            if (Auth::user()->active == 'SI')
            {
                $companies = Auth::user()->companies()->withoutGlobalScopes()->get();

                if ($companies->count() > 0)
                {
                    foreach ($companies as $val)
                    {
                        if ($val->pivot->active == 'NO')
                            continue;

                        $license = DB::table('sau_licenses')
                                ->whereRaw('company_id = ? 
                                            AND ? BETWEEN started_at AND ended_at', 
                                            [$val->pivot->company_id, date('Y-m-d')])
                                //->where('freeze', DB::raw("'NO'"))
                                ->get();

                        if (COUNT($license) > 0)
                        {
                            Session::put('company_id', $val->pivot->company_id);
                            $team = Team::where('name', Session::get('company_id'))->first()->id;

                            $user->update([
                                'validate_login' => true,
                                'code_login' => NULL
                            ]);

                            if (Auth::user()->hasRole('Arrendatario', $team) || Auth::user()->hasRole('Contratista', $team))
                            {
                                $contract = $this->getContractUserLogin(Auth::user()->id, $val->pivot->company_id);

                                if ($contract)
                                    Session::put('contract_id', $contract->id);
                                else
                                    continue;

                                if ($contract->active == 'SI')
                                {
                                    if ($contract->completed_registration == 'NO')
                                    {
                                        Auth::user()->update([
                                            'last_login_at' => Carbon::now()->toDateTimeString()
                                        ]);

                                        $this->userActivity('Inicio de sesión por token');
                                        
                                        return redirect('legalaspects/contracts/information');
                                    }

                                    return $this->defaultUrl(false);
                                }
                                else 
                                {
                                    Auth::logout();
                                    return $this->respondWithError(['errors'=>['email'=>'Estimado Usuario su contratista se encuentra inhabilitada para poder ingresar al sistema']], 422);
                                }
                            }

                            return $this->defaultUrl(false);
                            
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
        else
        {
            return $this->respondWithError(['errors'=>['email'=>'Email o Contraseña incorrecto']], 422);
        }
    }
}
