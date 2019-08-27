<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use App\Traits\ContractTrait;
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

                        if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
                        {
                            $contract = $this->getContractUser(Auth::user()->id);

                            if ($contract->active == 'SI')
                            {
                                Session::put('company_id', $val->pivot->company_id);
                            }
                        }

                        Session::put('company_id', $val->pivot->company_id);
                    }
                }
            }
        }

        if (!Session::get('company_id'))
            Auth::logout();
    }
}
