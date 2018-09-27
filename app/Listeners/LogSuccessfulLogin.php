<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
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
                break;
            }
        }

        if (!Session::get('company_id'))
            Session::put('company_id', null);
    }
}
