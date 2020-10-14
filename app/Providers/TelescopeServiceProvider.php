<?php

namespace App\Providers;

use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Models\General\Team;
use DB;
use Session;

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

            if (isset($request->user()->id) && Session::get('company_id'))
            {
                $team = Team::where('name', Session::get('company_id'))->first();

                if (!$request->user()->hasRole('Superadmin', $team)) {
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
        Telescope::night();

        Telescope::ignoreMigrations();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            return true;
            /*if ($this->app->isLocal()) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();*/
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
