<?php

namespace App\Providers;

use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use DB;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Telescope::auth(function ($request) {

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
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Telescope::night();

        Telescope::ignoreMigrations();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->isLocal()) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->isLocal()) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        /*Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });*/
    }
}
