<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use App\Traits\ContractTrait;
use App\Models\General\Team;
use Carbon\Carbon;
use Session;
use DB;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    use ContractTrait;

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);

        $valid = false;

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
                            ->first();

                    if ($license)
                    {
                        //Session::put('company_id', $val->pivot->company_id);
                        //return $this->respondHttp200();
                        //return $this->redirectTo('/');
                        Session::put('company_id', $val->pivot->company_id);
                        $team = Team::where('name', Session::get('company_id'))->first()->id;

                        if (Auth::user()->hasRole('Arrendatario', $team) || Auth::user()->hasRole('Contratista', $team))
                        {
                            $contract = $this->getContractUser(Auth::user()->id);

                            if ($contract->active == 'SI')
                            {
                                Auth::user()->update([
                                    'last_login_at' => Carbon::now()->toDateTimeString()
                                ]);

                                $valid = true;
                            }
                            else //Contratista inhabilitada
                            {
                                Auth::logout();
                            }
                        }
                        else
                        {
                            Auth::user()->update([
                                'last_login_at' => Carbon::now()->toDateTimeString()
                            ]);

                            $valid = true;
                        }
                    }
                }
                
                if (!$valid) //Sin licencia
                    Auth::logout();
            }
            else //Sin compañia activa
            {
                Auth::logout();
            }
        }
        else //usuario inhabilitado
        {
            Auth::logout();
        }

        if (!Session::get('company_id') || !$valid)
            Auth::logout();
    }
}
