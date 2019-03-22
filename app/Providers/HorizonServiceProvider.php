<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use DB;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        Horizon::auth(function ($request) {

            if (isset($request->user()->id))
            {
                $result = DB::table('sau_roles')
                            ->join('sau_role_user', 'sau_role_user.role_id', 'sau_roles.id')
                            ->where('sau_role_user.user_id', $request->user()->id)
                            ->where('sau_roles.name', 'Superadmin')
                            ->whereNull('sau_roles.company_id')
                            ->exists();

                if (!$result) {
                    throw new UnauthorizedHttpException('Unauthorized');
                }
            }
            else
                throw new UnauthorizedHttpException('Unauthorized');

            return true;
        });
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        /*Gate::define('viewHorizon', function ($user) {
            return in_array($user->email, [
            ]);
        });*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
